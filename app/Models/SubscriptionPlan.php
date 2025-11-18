<?php
// app/Models/SubscriptionPlan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'currency',
        'billing_cycle_days',
        'features',
        'max_courses',
        'max_ai_requests_per_month',
        'ai_grading',
        'priority_support',
        'is_active',
        'sort_order',
        'role',
        'tier',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'ai_grading' => 'boolean',
        'priority_support' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = ['formatted_price','monthly_price','yearly_price'];

    // Constants for plan roles
    const ROLE_STUDENT = 'student';
    const ROLE_TUTOR = 'tutor';
    const ROLE_ORGANIZATION = 'organization';

    // Constants for tiers
    const TIER_FREE = 'free';
    const TIER_BASIC = 'basic';
    const TIER_PRO = 'pro';
    const TIER_PREMIUM = 'premium';
    const TIER_STARTER = 'starter';
    const TIER_GROWTH = 'growth';
    const TIER_ENTERPRISE = 'enterprise';

    // Relationships
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPrice($query, $order = 'asc')
    {
        return $query->orderBy('price', $order);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeStudents($query)
    {
        return $this->scopeByRole($query, self::ROLE_STUDENT);
    }

    public function scopeTutors($query)
    {
        return $this->scopeByRole($query, self::ROLE_TUTOR);
    }

    public function scopeOrganizations($query)
    {
        return $this->scopeByRole($query, self::ROLE_ORGANIZATION);
    }



    // Methods
    public function getFormattedPrice(): string
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    public function getMonthlyPrice(): float
    {
        return $this->price;
    }

    public function getMonthlyPriceAttribute(): float
    {
        return $this->price;
    }


    public function getYearlyPrice(): float
    {
        return $this->price * 12 * 0.9; // 10% discount for yearly
    }

    public function getYearlyPriceAttribute(): float
    {
        return $this->price * 12 * 0.9; // 10% discount for yearly
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    public function isFree(): bool
    {
        return $this->price == 0;
    }

    public function isForStudents(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function isForTutors(): bool
    {
        return $this->role === self::ROLE_TUTOR;
    }

    public function isForOrganizations(): bool
    {
        return $this->role === self::ROLE_ORGANIZATION;
    }

    /**
     * Get recommended features for this plan
     */
    public function getRecommendedFeatures(): array
    {
        $features = [
            self::ROLE_STUDENT => $this->getStudentFeatures(),
            self::ROLE_TUTOR => $this->getTutorFeatures(),
            self::ROLE_ORGANIZATION => $this->getOrganizationFeatures(),
        ];

        return $features[$this->role] ?? [];
    }

    protected function getStudentFeatures(): array
    {
        return match($this->tier) {
            self::TIER_FREE => [
                'Limited AI chat per day',
                'Limited course creation',
            ],
            self::TIER_BASIC => [
                'X courses creation',
                'X AI explanations per day',
                'Save progress & history',
            ],
            self::TIER_PRO => [
                'Unlimited AI learning',
                'Full course library access',
                'Advanced explanations',
                'Voice explanations',
                'Exam preparation mode',
            ],
            self::TIER_PREMIUM => [
                'Everything in Pro',
                'Tutor-created custom lessons',
                'Personalized study plan',
                'Offline downloads',
                'Priority support',
            ],
            default => [],
        };
    }

    protected function getTutorFeatures(): array
    {
        return match($this->tier) {
            self::TIER_STARTER => [
                'Create up to X courses',
                'Manage up to X students',
                'Basic analytics',
            ],
            self::TIER_PRO => [
                'Unlimited courses',
                'Unlimited students',
                'Advanced analytics',
                'AI-assisted course creation',
                'AI quiz generator',
                'Payment integration',
            ],
            self::TIER_ENTERPRISE => [
                'Team collaboration',
                'Advanced AI content generation',
                'Video hosting + live classes',
                'Certificate generation',
                'Branded course pages',
            ],
            default => [],
        };
    }

    protected function getOrganizationFeatures(): array
    {
        return match($this->tier) {
            self::TIER_STARTER => [
                'Up to X students',
                'Basic dashboard',
                'Assignment & tracking',
                'Create school-wide courses',
            ],
            self::TIER_GROWTH => [
                'Unlimited students & tutors',
                'Attendance & reporting',
                'AI grading + feedback',
                'Custom branded portal',
                'Parent/guardian accounts',
            ],
            self::TIER_ENTERPRISE => [
                'SSO + security',
                'Dedicated support',
                'API access',
                'LMS/SIS integration',
                'Custom onboarding',
                'Custom SLAs',
            ],
            default => [],
        };
    }
}
