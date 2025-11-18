<?php
// app/Models/AiProvider.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'available_models',
        'api_key',
        'base_url',
        'cost_per_token',
        'max_tokens_per_request',
        'rate_limit_per_minute',
        'is_active',
        'is_default',
        'config',
    ];

    protected $casts = [
        'available_models' => 'array',
        'cost_per_token' => 'decimal:8',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'config' => 'array',
    ];

    // Relationships
    public function usageLogs(): HasMany
    {
        return $this->hasMany(AiUsageLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Methods
    public function getAvailableModelsArray()
    {
        $models = $this->available_models;

        // If it's already an array, return it
        if (is_array($models)) {
            return $models;
        }

        // If it's a string with backslashes, fix it
        if (is_string($models)) {
            // Remove backslashes and decode
            $cleaned = stripslashes($models);
            $decoded = json_decode($cleaned, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            // If that fails, try direct JSON decode
            $decoded = json_decode($models, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        // If all else fails, return empty array
        return [];
    }

    public function getConfigValue($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function calculateCost($promptTokens, $completionTokens)
    {
        $totalTokens = $promptTokens + $completionTokens;
        return $totalTokens * $this->cost_per_token;
    }

    public function isConfigured()
    {
        return !empty($this->api_key) && $this->is_active;
    }

    public function getModelDisplayName($model)
    {
        $modelNames = [
            'gpt-4' => 'GPT-4',
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            'deepseek-chat' => 'DeepSeek Chat',
            'deepseek-coder' => 'DeepSeek Coder',
        ];

        return $modelNames[$model] ?? $model;
    }
}
