<?php
// app/Models/CourseShare.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseShare extends Model
{
    protected $fillable = [
        'course_id',
        'sharer_id',
        'recipient_email',
        'token',
        'status',
        'expires_at',
        'accepted_at',
        'rejected_at',
        'recipient_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function sharer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sharer_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    public function markAsAccepted(User $user = null): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'recipient_id' => $user?->id,
        ]);
    }

    public function markAsRejected(): void
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '>', now());
    }

    public function scopeForRecipient($query, string $email)
    {
        return $query->where('recipient_email', $email);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('recipient_email', $user->email)
                ->orWhere('recipient_id', $user->id);
        });
    }

    public static function generateToken(): string
    {
        return hash('sha256', uniqid('course_share_', true));
    }
}
