<?php
// app/Models/AiUsageLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_provider_id',
        'user_id',
        'model_used',
        'endpoint',
        'purpose',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'cost',
        'success',
        'error_message',
        'request_metadata',
        'response_metadata',
    ];

    protected $casts = [
        'cost' => 'decimal:8',
        'success' => 'boolean',
        'request_metadata' => 'array',
        'response_metadata' => 'array',
    ];

    // Relationships
    public function aiProvider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeByPurpose($query, $purpose)
    {
        return $query->where('purpose', $purpose);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Methods
    public function getCostFormatted()
    {
        return '$' . number_format($this->cost, 6);
    }

    public function getTokensPerDollar()
    {
        return $this->cost > 0 ? $this->total_tokens / $this->cost : 0;
    }

    public function logSuccess($promptTokens, $completionTokens, $cost, $metadata = [])
    {
        $this->update([
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $promptTokens + $completionTokens,
            'cost' => $cost,
            'success' => true,
            'response_metadata' => $metadata,
        ]);
    }

    public function logFailure($errorMessage, $requestMetadata = [])
    {
        $this->update([
            'success' => false,
            'error_message' => $errorMessage,
            'request_metadata' => $requestMetadata,
        ]);
    }
}
