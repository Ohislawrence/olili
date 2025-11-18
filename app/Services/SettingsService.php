<?php
// app/Services/SettingsService.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsService
{
    protected $table = 'system_settings';
    protected $cacheKey = 'system_settings';
    protected $cacheTtl = 3600; // 1 hour

    public function get(string $key, $default = null)
    {
        $settings = $this->getAllSettings();

        return data_get($settings, $key, $default);
    }

    public function set(string $key, $value): bool
    {
        try {
            DB::table($this->table)->updateOrInsert(
                ['key' => $key],
                ['value' => json_encode($value), 'updated_at' => now()]
            );

            $this->clearCache();

            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to set setting {$key}: " . $e->getMessage());
            return false;
        }
    }

    public function setMultiple(array $settings): bool
    {
        try {
            DB::transaction(function () use ($settings) {
                foreach ($settings as $key => $value) {
                    DB::table($this->table)->updateOrInsert(
                        ['key' => $key],
                        ['value' => json_encode($value), 'updated_at' => now()]
                    );
                }
            });

            $this->clearCache();

            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to set multiple settings: " . $e->getMessage());
            return false;
        }
    }

    public function getAllSettings(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            return DB::table($this->table)
                ->pluck('value', 'key')
                ->map(function ($value) {
                    return json_decode($value, true);
                })
                ->toArray();
        });
    }

    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }
}
