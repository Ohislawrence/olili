<?php

namespace App\Services\WebPush;

use Exception;

class Encryption
{
    /**
     * Encrypt payload for Web Push
     */
    public function encrypt(
        string $payload,
        string $userPublicKey,
        string $userAuthSecret,
        string $serverPrivateKey
    ): array {
        // Decode base64url keys
        $userPublicKeyRaw = $this->base64url_decode($userPublicKey);
        $userAuthSecretRaw = $this->base64url_decode($userAuthSecret);
        $serverPrivateKeyRaw = $this->base64url_decode($serverPrivateKey);

        // Generate salt (16 bytes)
        $salt = random_bytes(16);

        // Generate ephemeral key pair for this message
        $localKeyPair = $this->generateKeyPair();
        $localPublicKey = $localKeyPair['public'];
        $localPrivateKey = $localKeyPair['private'];

        // Generate shared secret using ECDH
        $sharedSecret = $this->ecdh($localPrivateKey, $userPublicKeyRaw);

        // Generate PRK (Pseudorandom Key)
        $prk = hash_hmac('sha256', $sharedSecret, $userAuthSecretRaw, true);

        // Generate content encryption key
        $cekInfo = $this->createInfo('aesgcm', $userPublicKeyRaw, $localPublicKey);
        $contentEncryptionKey = $this->hkdf($prk, 16, $cekInfo, $salt);

        // Generate nonce
        $nonceInfo = $this->createInfo('nonce', $userPublicKeyRaw, $localPublicKey);
        $nonce = $this->hkdf($prk, 12, $nonceInfo, $salt);

        // Add padding (2 bytes for pad length + payload)
        $padding = "\x00\x00";
        $paddedPayload = $padding . $payload;

        // Encrypt using AES-GCM
        $ciphertext = openssl_encrypt(
            $paddedPayload,
            'aes-128-gcm',
            $contentEncryptionKey,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag
        );

        return [
            'ciphertext' => $ciphertext,
            'salt' => $this->base64url_encode($salt),
            'local_public_key' => $this->base64url_encode($localPublicKey),
            'auth_tag' => $this->base64url_encode($tag),
        ];
    }

    /**
     * Generate ephemeral key pair
     */
    private function generateKeyPair(): array
    {
        $config = [
            "curve_name" => "prime256v1",
            "private_key_type" => OPENSSL_KEYTYPE_EC,
        ];

        $keyPair = openssl_pkey_new($config);
        $details = openssl_pkey_get_details($keyPair);

        // Extract raw public key (remove the 0x04 prefix for uncompressed)
        $publicKey = substr($details['ec']['key'], 1);

        // Extract private key
        openssl_pkey_export($keyPair, $privateKeyPem);
        $privateKey = $this->extractPrivateKey($privateKeyPem);

        return [
            'public' => $publicKey,
            'private' => $privateKey,
        ];
    }

    /**
     * Perform ECDH key exchange
     */
    private function ecdh(string $privateKey, string $publicKey): string
    {
        // Create private key resource
        $privKey = openssl_pkey_new([
            'curve_name' => 'prime256v1',
            'ec' => ['curve_name' => 'prime256v1'],
            'private_key' => $privateKey,
        ]);

        // Create public key from raw bytes (add 0x04 for uncompressed)
        $publicKeyWithPrefix = "\x04" . $publicKey;

        // Generate shared secret
        $sharedSecret = '';
        openssl_pkey_derive($privKey, $publicKeyWithPrefix, $sharedSecret);

        return $sharedSecret;
    }

    /**
     * HKDF key derivation
     */
    private function hkdf(string $ikm, int $length, string $info, string $salt): string
    {
        // Extract
        $prk = hash_hmac('sha256', $ikm, $salt, true);

        // Expand
        $t = '';
        $last = '';
        $rounds = ceil($length / 32);

        for ($i = 1; $i <= $rounds; $i++) {
            $last = hash_hmac('sha256', $last . $info . chr($i), $prk, true);
            $t .= $last;
        }

        return substr($t, 0, $length);
    }

    /**
     * Create info string for HKDF
     */
    private function createInfo(string $type, string $clientPublicKey, string $serverPublicKey): string
    {
        return "Content-Encoding: {$type}\0" .
               "P-256\0" .
               "\x00\x41" . $clientPublicKey .
               "\x00\x41" . $serverPublicKey;
    }

    /**
     * Generate VAPID JWT
     */
    public function generateVapidJWT(string $audience, string $subject, string $privateKey): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'ES256',
        ];

        $payload = [
            'aud' => $audience,
            'exp' => time() + 43200, // 12 hours
            'sub' => $subject,
        ];

        $headerBase64 = $this->base64url_encode(json_encode($header));
        $payloadBase64 = $this->base64url_encode(json_encode($payload));

        $dataToSign = $headerBase64 . '.' . $payloadBase64;

        // Create private key from raw bytes
        $privateKeyRaw = $this->base64url_decode($privateKey);

        // Sign with ECDSA SHA256
        $signature = '';
        openssl_sign($dataToSign, $signature, $this->createPrivateKeyResource($privateKeyRaw), OPENSSL_ALGO_SHA256);

        // Convert DER signature to raw R|S format
        $signature = $this->derToRaw($signature);

        $signatureBase64 = $this->base64url_encode($signature);

        return $dataToSign . '.' . $signatureBase64;
    }

    /**
     * Convert DER signature to raw R|S format
     */
    private function derToRaw(string $der): string
    {
        // Skip ASN.1 header (assuming 70 bytes for P-256)
        $r = substr($der, 4, 32);
        $s = substr($der, 38, 32);

        return $r . $s;
    }

    /**
     * Create private key resource from raw bytes
     */
    private function createPrivateKeyResource(string $privateKeyRaw)
    {
        $pem = "-----BEGIN EC PRIVATE KEY-----\n" .
               base64_encode($this->privateKeyToDer($privateKeyRaw)) .
               "\n-----END EC PRIVATE KEY-----";

        return openssl_pkey_get_private($pem);
    }

    /**
     * Convert raw private key to DER format
     */
    private function privateKeyToDer(string $privateKeyRaw): string
    {
        // Simplified DER structure for EC private key
        $der = "\x30\x77" . // SEQUENCE, length 119
               "\x02\x01\x01" . // INTEGER, length 1, version 1
               "\x04\x20" . $privateKeyRaw . // OCTET STRING, length 32, private key
               "\xa0\x0a" . // [0], length 10
               "\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07" . // OID prime256v1
               "\xa1\x44" . // [1], length 68
               "\x03\x42\x00\x04"; // BIT STRING, length 66, public key (placeholder)

        // We don't need the actual public key for signing
        return $der;
    }

    private function extractPrivateKey(string $pem): string
    {
        $pem = str_replace([
            '-----BEGIN PRIVATE KEY-----',
            '-----END PRIVATE KEY-----',
            '-----BEGIN EC PRIVATE KEY-----',
            '-----END EC PRIVATE KEY-----',
            "\n",
            "\r",
            " "
        ], '', $pem);

        $der = base64_decode($pem);

        // Extract the private key from DER (simplified)
        // Private key is usually at offset 36 for EC keys
        return substr($der, 36, 32);
    }

    public function base64url_encode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    public function base64url_decode(string $data): string
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }
}
