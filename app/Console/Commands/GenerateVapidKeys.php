<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateVapidKeys extends Command
{
    protected $signature = 'vapid:generate';
    protected $description = 'Generate VAPID keys for Web Push Notifications';

    public function handle(): int
    {
        $this->info('Generating VAPID keys...');

        try {
            // Method 1: Try using OpenSSL (most reliable)
            $keys = $this->generateWithOpenSSL();

            $this->info('✅ VAPID keys generated successfully using OpenSSL!');

        } catch (\Exception $e) {
            $this->error('OpenSSL method failed: ' . $e->getMessage());
            $this->info('Trying alternative method...');

            // Method 2: Use sodium extension
            try {
                $keys = $this->generateWithSodium();
                $this->info('✅ VAPID keys generated successfully using Sodium!');
            } catch (\Exception $e) {
                $this->error('Sodium method failed: ' . $e->getMessage());
                $this->info('Generating dummy keys for development...');

                // Method 3: Generate simple keys for development
                $keys = $this->generateDummyKeys();
                $this->warn('⚠️ Generated dummy keys - NOT for production use!');
            }
        }

        $this->newLine();
        $this->info('Add these to your .env file:');
        $this->newLine();

        $this->line('VAPID_PUBLIC_KEY=' . $keys['publicKey']);
        $this->line('VAPID_PRIVATE_KEY=' . $keys['privateKey']);
        $this->line('VAPID_SUBJECT=mailto:' . config('mail.from.address', 'admin@example.com'));

        // Also show for frontend
        $this->newLine();
        $this->info('For frontend (Vite/.env):');
        $this->line('VITE_VAPID_PUBLIC_KEY=' . $keys['publicKey']);

        // Create config file if it doesn't exist
        if (!file_exists(config_path('webpush.php'))) {
            $config = [
                'vapid' => [
                    'subject' => 'mailto:' . config('mail.from.address', 'admin@example.com'),
                    'public_key' => $keys['publicKey'],
                    'private_key' => $keys['privateKey'],
                ],
            ];

            file_put_contents(
                config_path('webpush.php'),
                '<?php return ' . var_export($config, true) . ';'
            );

            $this->info('✅ Config file created: config/webpush.php');
        }

        // Save to temporary file for easy copying
        file_put_contents(
            storage_path('app/vapid_keys.txt'),
            "VAPID_PUBLIC_KEY={$keys['publicKey']}\n" .
            "VAPID_PRIVATE_KEY={$keys['privateKey']}\n" .
            "VAPID_SUBJECT=mailto:" . config('mail.from.address', 'admin@example.com') . "\n" .
            "VITE_VAPID_PUBLIC_KEY={$keys['publicKey']}"
        );

        $this->newLine();
        $this->info('Keys also saved to: ' . storage_path('app/vapid_keys.txt'));

        return Command::SUCCESS;
    }

    private function generateWithOpenSSL(): array
    {
        if (!extension_loaded('openssl')) {
            throw new \Exception('OpenSSL extension is not loaded');
        }

        // Generate EC key pair
        $config = [
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_EC,
            "curve_name" => "prime256v1"
        ];

        // Generate key pair
        $keyPair = openssl_pkey_new($config);

        if (!$keyPair) {
            throw new \Exception('Failed to generate key pair');
        }

        // Export private key
        openssl_pkey_export($keyPair, $privateKeyPem);

        // Get public key details
        $keyDetails = openssl_pkey_get_details($keyPair);

        if (!$keyDetails) {
            throw new \Exception('Failed to get key details');
        }

        // Extract keys from PEM format
        $privateKey = $this->extractPrivateKey($privateKeyPem);
        $publicKey = $this->extractPublicKey($keyDetails['key']);

        return [
            'publicKey' => $this->base64url_encode($publicKey),
            'privateKey' => $this->base64url_encode($privateKey),
        ];
    }

    private function generateWithSodium(): array
    {
        if (!extension_loaded('sodium')) {
            throw new \Exception('Sodium extension is not loaded');
        }

        // Generate a key pair
        $keyPair = sodium_crypto_box_keypair();
        $publicKey = sodium_crypto_box_publickey($keyPair);
        $privateKey = sodium_crypto_box_secretkey($keyPair);

        return [
            'publicKey' => $this->base64url_encode($publicKey),
            'privateKey' => $this->base64url_encode($privateKey),
        ];
    }

    private function generateDummyKeys(): array
    {
        // Generate random base64url strings for development
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';

        // Public key should start with 'BL' and be 87 characters
        $publicKey = 'BL' . substr(str_shuffle(str_repeat($chars, 10)), 0, 85);

        // Private key should be 43 characters
        $privateKey = substr(str_shuffle(str_repeat($chars, 10)), 0, 43);

        return [
            'publicKey' => $publicKey,
            'privateKey' => $privateKey,
        ];
    }

    private function extractPrivateKey(string $pem): string
    {
        // Remove PEM headers and whitespace
        $pem = str_replace([
            '-----BEGIN PRIVATE KEY-----',
            '-----END PRIVATE KEY-----',
            "\n",
            "\r",
            " "
        ], '', $pem);

        return base64_decode($pem);
    }

    private function extractPublicKey(string $pem): string
    {
        // Remove PEM headers and whitespace
        $pem = str_replace([
            '-----BEGIN PUBLIC KEY-----',
            '-----END PUBLIC KEY-----',
            "\n",
            "\r",
            " "
        ], '', $pem);

        return base64_decode($pem);
    }

    private function base64url_encode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
