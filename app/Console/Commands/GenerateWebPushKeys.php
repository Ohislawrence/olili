<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Crypto\VapidKeys;

class GenerateWebPushKeys extends Command
{
    protected $signature = 'webpush:generate-keys';
    protected $description = 'Generate VAPID keys for Web Push';

    public function handle(): int
    {
        $this->info('Generating VAPID keys...');

        try {
            $keys = VapidKeys::generate();

            $this->info('✅ Keys generated successfully!');
            $this->newLine();

            $this->info('Public Key:');
            $this->line($keys['public_key']);
            $this->newLine();

            $this->info('Private Key:');
            $this->line($keys['private_key']);
            $this->newLine();

            $this->info('Add to your .env file:');
            $this->line('VAPID_PUBLIC_KEY=' . $keys['public_key']);
            $this->line('VAPID_PRIVATE_KEY=' . $keys['private_key']);
            $this->line('VAPID_SUBJECT=mailto:' . config('mail.from.address', 'admin@example.com'));

            // Save to file for backup
            file_put_contents(storage_path('app/vapid_keys.json'), json_encode($keys, JSON_PRETTY_PRINT));

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to generate keys: ' . $e->getMessage());

            // Generate simple keys for development
            $this->warn('Generating development keys...');

            $devKeys = [
                'public_key' => 'BL' . str_repeat('A', 85),
                'private_key' => str_repeat('B', 43),
            ];

            $this->info('Development keys (not for production):');
            $this->line('VAPID_PUBLIC_KEY=' . $devKeys['public_key']);
            $this->line('VAPID_PRIVATE_KEY=' . $devKeys['private_key']);

            return Command::SUCCESS;
        }
    }
}
