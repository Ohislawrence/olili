<?php
// app/Models/Subscription.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'paystack_subscription_code',
        'paystack_customer_code',
        'authorization_code',
        'card_type',
        'last4',
        'starts_at',
        'ends_at',
        'canceled_at',
        'trial_ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'canceled');
    }

    public function scopeByRole($query, string $role)
    {
        return $query->whereHas('plan', function ($q) use ($role) {
            $q->where('role', $role);
        });
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function isCancelled(): bool
    {
        return $this->status === 'canceled';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || $this->ends_at->isPast();
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'ends_at' => now(),
        ]);
    }

    public function renew(): void
    {
        $this->update([
            'status' => 'active',
            'ends_at' => now()->addDays($this->plan->billing_cycle_days),
        ]);
    }

    public function daysUntilExpiration(): int
    {
        return now()->diffInDays($this->ends_at, false);
    }

    public function hasFeature(string $feature): bool
    {
        return $this->plan->hasFeature($feature);
    }

    public function canCreateMoreCourses(): bool
    {
        if ($this->plan->max_courses === -1) return true;

        $currentCourses = $this->user->courses()->count();
        return $currentCourses < $this->plan->max_courses;
    }

    public function canMakeAiRequest(): bool
    {
        if ($this->plan->max_ai_requests_per_month === -1) return true;

        $monthlyUsage = $this->user->aiUsageLogs()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        return $monthlyUsage < $this->plan->max_ai_requests_per_month;
    }

    /**
     * Check if user can manage more students (for tutors/organizations)
     */
    public function canManageMoreStudents(): bool
    {
        if ($this->plan->max_students === -1) return true;

        $currentStudents = $this->user->students()->count();
        return $currentStudents < $this->plan->max_students;
    }

    /**
     * Check if organization can manage more tutors
     */
    public function canManageMoreTutors(): bool
    {
        if ($this->plan->max_tutors === -1) return true;

        $currentTutors = $this->user->organization->tutors()->count();
        return $currentTutors < $this->plan->max_tutors;
    }
}
