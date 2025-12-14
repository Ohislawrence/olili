<?php

namespace App\Services\Crypto;

use Exception;

class VapidKeys
{
    public static function generate(): array
    {
        // Generate EC key pair using OpenSSL
        $config = [
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_EC,
            "curve_name" => "prime256v1"
        ];

        // Generate key pair
        $keyPair = openssl_pkey_new($config);

        if (!$keyPair) {
            throw new Exception('Failed to generate key pair');
        }

        // Export private key
        openssl_pkey_export($keyPair, $privateKeyPem);

        // Get public key
        $publicKeyDetails = openssl_pkey_get_details($keyPair);
        $publicKeyPem = $publicKeyDetails['key'];

        // Extract raw keys
        $privateKey = self::extractKeyFromPem($privateKeyPem, 'PRIVATE');
        $publicKey = self::extractKeyFromPem($publicKeyPem, 'PUBLIC');

        return [
            'public_key' => self::base64url_encode($publicKey),
            'private_key' => self::base64url_encode($privateKey),
        ];
    }

    private static function extractKeyFromPem(string $pem, string $type): string
    {
        $pem = str_replace([
            "-----BEGIN {$type} KEY-----",
            "-----END {$type} KEY-----",
            "\n",
            "\r"
        ], '', $pem);

        return base64_decode($pem);
    }

    public static function base64url_encode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    public static function base64url_decode(string $data): string
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }

    public static function getPublicKeyFromPrivate(string $privateKeyBase64): string
    {
        $privateKeyRaw = self::base64url_decode($privateKeyBase64);

        // Create private key resource
        $privateKey = openssl_pkey_new([
            'curve_name' => 'prime256v1',
            'ec' => ['curve_name' => 'prime256v1'],
            'private_key' => $privateKeyRaw,
            'd' => $privateKeyRaw,
        ]);

        $details = openssl_pkey_get_details($privateKey);
        $publicKeyRaw = $details['ec']['key'];

        return self::base64url_encode($publicKeyRaw);
    }
}
