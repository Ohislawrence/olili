<?php
// app/Models/LoginHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'country',
        'city',
        'login_type',
        'was_successful',
        'failure_reason',
        'login_at',
        'logout_at',
        'session_duration_seconds',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'was_successful' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('was_successful', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('was_successful', false);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('login_at', '>=', now()->subDays($days));
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByIp($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    // Methods
    public function recordLogout(): void
    {
        $this->update([
            'logout_at' => now(),
            'session_duration_seconds' => $this->login_at->diffInSeconds(now()),
        ]);
    }

    public function getSessionDurationFormatted(): string
    {
        if (!$this->session_duration_seconds) {
            return 'Active';
        }

        $hours = floor($this->session_duration_seconds / 3600);
        $minutes = floor(($this->session_duration_seconds % 3600) / 60);
        $seconds = $this->session_duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $seconds);
        }

        if ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        }

        return sprintf('%ds', $seconds);
    }

    public function getLocation(): string
    {
        if ($this->city && $this->country) {
            return $this->city . ', ' . $this->country;
        }

        return $this->country ?? 'Unknown';
    }

    public function isCurrentSession(): bool
    {
        return is_null($this->logout_at);
    }
}
