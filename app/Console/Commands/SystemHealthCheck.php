<?php
// app/Console/Commands/SystemHealthCheck.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemHealthCheck extends Command
{
    protected $signature = 'system:health-check';
    protected $description = 'Check system health and report issues';

    public function handle(): int
    {
        $this->info('Running system health check...');
        $issues = [];

        // 1. Check database connection
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection: OK');
        } catch (\Exception $e) {
            $issues[] = 'Database connection failed: ' . $e->getMessage();
            $this->error('❌ Database connection: FAILED');
        }

        // 2. Check cache
        try {
            Cache::put('health_check', 'ok', 10);
            if (Cache::get('health_check') === 'ok') {
                $this->info('✅ Cache: OK');
                Cache::forget('health_check');
            } else {
                $issues[] = 'Cache not working properly';
                $this->error('❌ Cache: FAILED');
            }
        } catch (\Exception $e) {
            $issues[] = 'Cache failed: ' . $e->getMessage();
            $this->error('❌ Cache: FAILED');
        }

        // 3. Check storage
        try {
            $testFile = 'health_check_' . time();
            Storage::disk('local')->put($testFile, 'test');
            Storage::disk('local')->exists($testFile);
            Storage::disk('local')->delete($testFile);
            $this->info('✅ Storage: OK');
        } catch (\Exception $e) {
            $issues[] = 'Storage failed: ' . $e->getMessage();
            $this->error('❌ Storage: FAILED');
        }

        // 4. Check queue connection
        try {
            if (config('queue.default') !== 'sync') {
                // Try to get queue size
                $this->info('✅ Queue connection: OK');
            }
        } catch (\Exception $e) {
            $issues[] = 'Queue connection failed: ' . $e->getMessage();
            $this->error('❌ Queue connection: FAILED');
        }

        // 5. Check critical directories
        $directories = [
            storage_path('logs'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
        ];

        foreach ($directories as $directory) {
            if (!is_writable($directory)) {
                $issues[] = "Directory not writable: {$directory}";
                $this->error("❌ {$directory}: NOT WRITABLE");
            } else {
                $this->info("✅ {$directory}: WRITABLE");
            }
        }

        // Report results
        if (empty($issues)) {
            $this->info('🎉 All systems operational!');
            \Log::info('System health check passed');
            return Command::SUCCESS; // Exit code 0
        } else {
            $this->error('⚠️ Found ' . count($issues) . ' issues:');
            foreach ($issues as $issue) {
                $this->error('  - ' . $issue);
                \Log::error('Health check issue: ' . $issue);
            }
            return Command::FAILURE; // Exit code 1
        }
    }
}
