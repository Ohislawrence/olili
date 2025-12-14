<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PushSubscription extends Model
{
    protected $table = 'push_subscriptions';

    protected $fillable = [
        'user_id',
        'endpoint',
        'p256dh_key',
        'auth_key',
        'user_agent',
        'metadata',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific endpoint
     */
    public function scopeEndpoint($query, $endpoint)
    {
        return $query->where('endpoint', $endpoint);
    }

    /**
     * Mark subscription as used
     */
    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Deactivate subscription
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Get decoded p256dh key
     */
    protected function decodedP256dhKey(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->base64url_decode($this->p256dh_key)
        );
    }

    /**
     * Get decoded auth key
     */
    protected function decodedAuthKey(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->base64url_decode($this->auth_key)
        );
    }

    /**
     * Helper to decode base64url
     */
    private function base64url_decode(string $data): string
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * Convert subscription to array for web push library
     */
    public function toWebPushArray(): array
    {
        return [
            'endpoint' => $this->endpoint,
            'publicKey' => $this->p256dh_key,
            'authToken' => $this->auth_key,
            'contentEncoding' => 'aesgcm',
        ];
    }
}
