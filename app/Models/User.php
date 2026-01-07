<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Http;
use App\Traits\HasOnboarding;




class User extends Authenticatable
{
    use HasApiTokens;
    use HasRoles;
    use HasOnboarding;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'gender',
        'bio',
        'is_active',
        'last_login_at',
        'preferences',
        'provider',
        'provider_id',
        'provider_token',
        'consecutive_login_days',
        'failed_login_attempts',
        'account_locked_until',
        'last_login_ip',
        'login_count',
        'last_failed_login_at',
        'onboarding_completed_at',
        'onboarding_skipped_at',
        'onboarding_seen_at',
        'onboarding_data',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'last_login_at' => 'datetime',
            'preferences' => 'array',
            'is_active' => 'boolean',
            'consecutive_login_days' => 'integer',
            'failed_login_attempts' => 'integer',
            'account_locked_until' => 'datetime',
            'login_count' => 'integer',
            'onboarding_completed_at' => 'datetime',
            'onboarding_skipped_at' => 'datetime',
            'onboarding_seen_at' => 'datetime',
            'onboarding_data' => 'array',
        ];
    }

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function hasCompletedProfile(): bool
    {
        return $this->studentProfile !== null;
    }

    public function courses()
    {
        return $this->hasManyThrough(
            Course::class,
            StudentProfile::class,
            'user_id', // Foreign key on student_profiles table
            'student_profile_id', // Foreign key on courses table
            'id', // Local key on users table
            'id' // Local key on student_profiles table
        )->select('courses.*', 'student_profiles.user_id as laravel_through_key');
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class);
    }

    public function capstoneProjects()
    {
        return $this->hasManyThrough(CapstoneProject::class, Course::class, 'student_profile_id')
            ->through('studentProfile');
    }

    public function aiUsageLogs()
    {
        return $this->hasMany(AiUsageLog::class);
    }

    public function progressTracking()
    {
        return $this->hasMany(ProgressTracking::class);
    }

    // Scopes
    public function scopeStudents($query)
    {
        return $query->role('student');
    }

    public function scopeTutors($query)
    {
        return $query->role('tutor');
    }

    public function scopeAdmins($query)
    {
        return $query->role('admin');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function isStudent()
    {
        return $this->hasRole('student');
    }

    public function isTutor()
    {
        return $this->hasRole('tutor');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function getActiveCourse()
    {
        return $this->courses()->where('status', 'active')->first();
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function recordLogin(string $loginType = 'password', bool $wasSuccessful = true, ?string $failureReason = null): LoginHistory
    {
        $ipAddress = request()->ip();
        $userAgent = request()->userAgent();

        $loginHistory = LoginHistory::create([
            'user_id' => $this->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_type' => $this->getDeviceType($userAgent),
            'browser' => $this->getBrowser($userAgent),
            'platform' => $this->getPlatform($userAgent),
            'country' => $this->getCountryFromIp($ipAddress),
            'city' => $this->getCityFromIp($ipAddress),
            'login_type' => $loginType,
            'was_successful' => $wasSuccessful,
            'failure_reason' => $failureReason,
            'login_at' => now(),
        ]);

        if ($wasSuccessful) {
            // Ensure all fields have proper default values
            $updateData = [
                'last_login_at' => now(),
                'last_login_ip' => $ipAddress,
                'login_count' => ($this->login_count ?? 0) + 1,
                'consecutive_login_days' => $this->calculateConsecutiveLoginDays(),
                'failed_login_attempts' => 0, // Reset failed attempts on successful login
                'account_locked_until' => null, // Unlock account
            ];

            $this->update($updateData);
        } else {
            $this->increment('failed_login_attempts', 1, [
                'failed_login_attempts' => $this->failed_login_attempts ?? 0
            ]);

            $this->update([
                'last_failed_login_at' => now(),
            ]);

            // Lock account after 5 failed attempts for 30 minutes
            if (($this->failed_login_attempts ?? 0) >= 5) {
                $this->update([
                    'account_locked_until' => now()->addMinutes(30),
                ]);
            }
        }

        return $loginHistory;
    }

    public function recordLogout(): void
    {
        $currentSession = $this->loginHistories()
            ->whereNull('logout_at')
            ->latest()
            ->first();

        if ($currentSession) {
            $currentSession->recordLogout();
        }
    }

    public function isAccountLocked(): bool
    {
        return $this->account_locked_until && $this->account_locked_until->isFuture();
    }

    public function getRemainingLockTime(): ?int
    {
        if (!$this->isAccountLocked()) {
            return null;
        }

        return now()->diffInMinutes($this->account_locked_until);
    }

    public function getLoginStats(): array
    {
        $totalLogins = $this->loginHistories()->successful()->count();
        $failedLogins = $this->loginHistories()->failed()->count();
        $uniqueIps = $this->loginHistories()->distinct('ip_address')->count('ip_address');

        return [
            'total_logins' => $totalLogins,
            'failed_logins' => $failedLogins,
            'success_rate' => $totalLogins > 0 ? round(($totalLogins / ($totalLogins + $failedLogins)) * 100, 1) : 0,
            'unique_ips' => $uniqueIps,
            'consecutive_days' => $this->consecutive_login_days,
        ];
    }

    public function getRecentLogins($limit = 10)
    {
        return $this->loginHistories()
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    protected function calculateConsecutiveLoginDays(): int
    {
        $lastLogin = $this->last_login_at;

        if (!$lastLogin) {
            return 1;
        }

        $yesterday = now()->subDay()->startOfDay();
        $lastLoginDate = $lastLogin->startOfDay();

        // Get current consecutive days, default to 0 if null
        $currentConsecutiveDays = $this->consecutive_login_days ?? 0;

        if ($lastLoginDate->equalTo($yesterday)) {
            return $currentConsecutiveDays + 1;
        } elseif ($lastLoginDate->equalTo(now()->startOfDay())) {
            return $currentConsecutiveDays; // Same day login
        } else {
            return 1; // Break in consecutive days
        }
    }

    protected function getDeviceType($userAgent): string
    {
        $mobileAgents = ['Mobile', 'Android', 'iPhone', 'iPad', 'Windows Phone'];

        foreach ($mobileAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                if (stripos($userAgent, 'Tablet') !== false || stripos($userAgent, 'iPad') !== false) {
                    return 'tablet';
                }
                return 'mobile';
            }
        }

        return 'desktop';
    }

    protected function getBrowser($userAgent): string
    {
        $browsers = [
            'Chrome' => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari' => 'Safari',
            'Edge' => 'Edge',
            'Opera' => 'Opera',
            'MSIE' => 'Internet Explorer',
        ];

        foreach ($browsers as $pattern => $browser) {
            if (stripos($userAgent, $pattern) !== false) {
                return $browser;
            }
        }

        return 'Unknown';
    }

    protected function getPlatform($userAgent): string
    {
        $platforms = [
            'Windows' => 'Windows',
            'Macintosh' => 'macOS',
            'Linux' => 'Linux',
            'Android' => 'Android',
            'iPhone' => 'iOS',
            'iPad' => 'iOS',
        ];

        foreach ($platforms as $pattern => $platform) {
            if (stripos($userAgent, $pattern) !== false) {
                return $platform;
            }
        }

        return 'Unknown';
    }

    protected function getCountryFromIp($ipAddress): ?string
    {
        try {
            // Using a simple IP geolocation service
            // You might want to use a dedicated package like geoip/geoip
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ipAddress}");

            if ($response->successful()) {
                $data = $response->json();
                return $data['country'] ?? null;
            }
        } catch (\Exception $e) {
            // Log error or use fallback
            \Log::debug("Failed to get country for IP {$ipAddress}: " . $e->getMessage());
        }

        return null;
    }

    protected function getCityFromIp($ipAddress): ?string
    {
        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ipAddress}");

            if ($response->successful()) {
                $data = $response->json();
                return $data['city'] ?? null;
            }
        } catch (\Exception $e) {
            \Log::debug("Failed to get city for IP {$ipAddress}: " . $e->getMessage());
        }

        return null;
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the active subscription for the user.
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest();
    }

    /**
     * Get the current subscription for the user (active or trial).
     */
    public function currentSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where(function ($query) {
                $query->where('status', 'active')
                    ->orWhere(function ($q) {
                        $q->whereNotNull('trial_ends_at')
                            ->where('trial_ends_at', '>', now());
                    });
            })
            ->latest();
    }

    /**
     * Check if the user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Check if the user is on trial.
     */
    public function onTrial(): bool
    {
        $subscription = $this->currentSubscription();
        return $subscription && $subscription->isOnTrial();
    }

    /**
     * Check if the user has any subscription (active, trial, or cancelled).
     */
    public function hasSubscription(): bool
    {
        return $this->subscriptions()->exists();
    }

    /**
     * Scope a query to only include users with active subscriptions.
     */
    public function scopeSubscribed($query)
    {
        return $query->whereHas('subscriptions', function ($q) {
            $q->where('status', 'active')
                ->where('ends_at', '>', now());
        });
    }

    /**
     * Scope a query to only include users on trial.
     */
    public function scopeOnTrial($query)
    {
        return $query->whereHas('subscriptions', function ($q) {
            $q->whereNotNull('trial_ends_at')
                ->where('trial_ends_at', '>', now());
        });
    }

    protected function parseDeviceType(string $userAgent): string
    {
        if (strpos($userAgent, 'Mobile') !== false) {
            return 'Mobile';
        } elseif (strpos($userAgent, 'Tablet') !== false) {
            return 'Tablet';
        }
        return 'Desktop';
    }

    protected function parseBrowser(string $userAgent): string
    {
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        return 'Unknown';
    }

    public function tutorProfile()
    {
        return $this->hasOne(TutorProfile::class);
    }

    /**
     * Organization profile relationship
     */
    public function organizationProfile()
    {
        return $this->hasOne(OrganizationProfile::class);
    }

    /**
     * Check if user has tutor profile
     */
    public function hasTutorProfile(): bool
    {
        return $this->tutorProfile !== null;
    }

    /**
     * Check if user has organization profile
     */
    public function hasOrganizationProfile(): bool
    {
        return $this->organizationProfile !== null;
    }

    /**
     * Get all profiles (student, tutor, organization)
     */
    public function getProfiles(): array
    {
        $profiles = [];

        if ($this->studentProfile) {
            $profiles[] = [
                'type' => 'student',
                'profile' => $this->studentProfile,
            ];
        }

        if ($this->tutorProfile) {
            $profiles[] = [
                'type' => 'tutor',
                'profile' => $this->tutorProfile,
            ];
        }

        if ($this->organizationProfile) {
            $profiles[] = [
                'type' => 'organization',
                'profile' => $this->organizationProfile,
            ];
        }

        return $profiles;
    }

    /**
     * Get primary profile based on roles
     */
    public function getPrimaryProfile()
    {
        if ($this->isAdmin()) {
            return null; // Admin doesn't have a profile
        }

        if ($this->isStudent() && $this->studentProfile) {
            return $this->studentProfile;
        }

        if ($this->isTutor() && $this->tutorProfile) {
            return $this->tutorProfile;
        }

        if ($this->hasRole('organization') && $this->organizationProfile) {
            return $this->organizationProfile;
        }

        return null;
    }

    public function getCurrentSubscriptionAttribute(): ?Subscription
    {
        $subscription = $this->currentSubscription()->with('plan')->first();

        // Debug: Log if subscription exists but plan doesn't
        if ($subscription && !$subscription->plan) {
            \Log::warning('Subscription plan not found', [
                'subscription_id' => $subscription->id,
                'plan_id' => $subscription->subscription_plan_id,
                'user_id' => $this->id
            ]);
        }

        return $subscription;
    }

    public function getActiveSubscriptionAttribute(): ?Subscription
    {
        return $this->activeSubscription()->first();
    }

    public function getHasActiveSubscriptionAttribute(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function getIsOnTrialAttribute(): bool
    {
        $subscription = $this->currentSubscription;
        return $subscription && $subscription->isOnTrial();
    }

    /**
     * Get current subscription plan
     */
    public function getCurrentPlanAttribute(): ?SubscriptionPlan
    {
        $subscription = $this->currentSubscription;
        return $subscription ? $subscription->plan : null;
    }

    public function canAccessFeature(string $feature): bool
    {
        $subscription = $this->current_subscription;

        if (!$subscription) {
            // Check if feature is available in free tier
            return in_array($feature, $this->getFreeFeatures());
        }

        return $subscription->hasFeature($feature);
    }

    /**
     * Get free features based on user role
     */
    protected function getFreeFeatures(): array
    {
        $baseFeatures = ['view_courses', 'basic_chat', 'view_profile'];

        if ($this->isStudent()) {
            return array_merge($baseFeatures, [
                'take_quizzes',
                'view_progress',
            ]);
        }

        if ($this->isTutor()) {
            return array_merge($baseFeatures, [
                'view_students',
                'basic_analytics',
            ]);
        }

        return $baseFeatures;
    }

    /**
     * Check if user can create more courses
     */
    public function canCreateMoreCourses(): bool
    {
        $subscription = $this->current_subscription;

        if (!$subscription) {
            // Free tier limit - adjust as needed
            return $this->courses()->count() < 1;
        }

        return $subscription->canCreateMoreCourses();
    }

    /**
     * Check if user can make AI requests
     */
    public function canMakeAiRequest(): bool
    {
        $subscription = $this->current_subscription;

        if (!$subscription) {
            // Free tier limit - adjust as needed
            $monthlyUsage = $this->aiUsageLogs()
                ->where('created_at', '>=', now()->startOfMonth())
                ->count();
            return $monthlyUsage < 10; // 10 free AI requests per month
        }

        return $subscription->canMakeAiRequest();
    }

    /**
     * Get subscription status
     */
    public function getSubscriptionStatus(): array
    {
        $subscription = $this->current_subscription;

        if (!$subscription) {
            return [
                'has_subscription' => false,
                'status' => 'none',
                'plan' => null,
                'is_trial' => false,
                'days_remaining' => 0,
            ];
        }

        return [
            'has_subscription' => true,
            'status' => $subscription->status,
            'plan' => $subscription->plan,
            'is_trial' => $subscription->isOnTrial(),
            'days_remaining' => $subscription->daysUntilExpiration(),
            'is_active' => $subscription->isActive(),
        ];
    }

    public function canBeImpersonated()
    {
        // Prevent impersonating other admins
        return !$this->hasRole('admin');
    }

    /**
     * Check if this user can impersonate others
     */
    public function canImpersonate()
    {
        // Only admins can impersonate
        return $this->hasRole('admin');
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function hasSocialAccount(string $provider): bool
    {
        return $this->socialAccounts()->where('provider', $provider)->exists();
    }

    public function communityPosts()
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function postComments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function postLikes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function followers()
    {
        return $this->hasMany(UserFollower::class, 'user_id')->with('follower');
    }

    public function following()
    {
        return $this->hasMany(UserFollower::class, 'follower_id')->with('user');
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('user_id', $user->id)->exists();
    }

    public function follow(User $user): void
    {
        if (!$this->isFollowing($user)) {
            UserFollower::create([
                'user_id' => $user->id,
                'follower_id' => $this->id,
            ]);
        }
    }

    public function unfollow(User $user): void
    {
        $this->following()->where('user_id', $user->id)->delete();
    }

    public function getFollowerCountAttribute(): int
    {
        return $this->followers()->count();
    }

    public function getFollowingCountAttribute(): int
    {
        return $this->following()->count();
    }

    public function getPostCountAttribute(): int
    {
        return $this->communityPosts()->count();
    }

    public function getCommunityStatsAttribute(): array
    {
        return [
            'posts' => $this->post_count,
            'followers' => $this->follower_count,
            'following' => $this->following_count,
            'likes_received' => $this->communityPosts()->sum('like_count'),
            'comments_received' => $this->communityPosts()->sum('comment_count'),
        ];
    }

    public function webPushSubscriptions()
    {
        return $this->hasMany(WebPushSubscription::class);
    }

    public function pushNotifications()
    {
        return $this->hasMany(PushNotification::class);
    }


     public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    /**
     * Get active push subscriptions
     */
    public function activePushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class)->active();
    }

    /**
     * Check if user has any active push subscriptions
     */
    public function hasPushSubscription(): bool
    {
        return $this->pushSubscriptions()->active()->exists();
    }

    /**
     * Get push subscription by endpoint
     */
    public function pushSubscriptionByEndpoint(string $endpoint)
    {
        return $this->pushSubscriptions()->endpoint($endpoint)->first();
    }

    /**
     * Add or update a push subscription
     */
    public function addPushSubscription(array $data): PushSubscription
    {
        return $this->pushSubscriptions()->updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'p256dh_key' => $data['keys']['p256dh'],
                'auth_key' => $data['keys']['auth'],
                'user_agent' => request()->userAgent(),
                'metadata' => [
                    'browser' => $this->getBrowserInfo(),
                    'platform' => $this->getPlatformInfo(),
                    'ip_address' => request()->ip(),
                ],
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Remove a push subscription by endpoint
     */
    public function removePushSubscription(string $endpoint): bool
    {
        return $this->pushSubscriptions()
            ->where('endpoint', $endpoint)
            ->delete() > 0;
    }

    /**
     * Deactivate all push subscriptions
     */
    public function deactivateAllPushSubscriptions(): void
    {
        $this->pushSubscriptions()->update(['is_active' => false]);
    }

    /**
     * Get browser information from user agent
     */
    private function getBrowserInfo(): array
    {
        $userAgent = request()->userAgent();

        return [
            'user_agent' => $userAgent,
            'browser' => $this->parseBrowser($userAgent),
            'version' => $this->parseBrowserVersion($userAgent),
        ];
    }

    /**
     * Get platform information
     */
    private function getPlatformInfo(): array
    {
        $userAgent = request()->userAgent();

        return [
            'platform' => $this->parsePlatform($userAgent),
            'is_mobile' => $this->isMobile($userAgent),
            'is_desktop' => $this->isDesktop($userAgent),
        ];
    }



    /**
     * Parse browser version (simplified)
     */
    private function parseBrowserVersion(string $userAgent): string
    {
        // Simplified version parsing
        preg_match('/(Chrome|Firefox|Safari|Edge|Opera)\/([0-9.]+)/', $userAgent, $matches);

        return $matches[2] ?? 'Unknown';
    }

    /**
     * Parse platform from user agent
     */
    private function parsePlatform(string $userAgent): string
    {
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac') !== false) return 'macOS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iPhone') !== false) return 'iOS';

        return 'Unknown';
    }

    /**
     * Check if user agent is mobile
     */
    private function isMobile(string $userAgent): bool
    {
        return preg_match('/(android|iphone|ipad|mobile)/i', $userAgent) === 1;
    }

    /**
     * Check if user agent is desktop
     */
    private function isDesktop(string $userAgent): bool
    {
        return !$this->isMobile($userAgent);
    }

    /**
     * Get all active subscription endpoints
     */
    public function getPushEndpoints(): array
    {
        return $this->activePushSubscriptions()
            ->pluck('endpoint')
            ->toArray();
    }

    /**
     * Get push subscription count
     */
    public function getPushSubscriptionCount(): int
    {
        return $this->pushSubscriptions()->active()->count();
    }

    /**
     * Get latest push subscription
     */
    public function latestPushSubscription()
    {
        return $this->hasOne(PushSubscription::class)->latestOfMany();
    }

    /**
     * Route notifications for web push
     * This is used by Laravel's notification system
     */
    public function routeNotificationForWebPush()
    {
        return $this->activePushSubscriptions;
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments', 'user_id', 'course_id')
            ->using(CourseEnrollment::class)
            ->withPivot([
                'id', 'status', 'progress_percentage', 'enrolled_at',
                'started_at', 'completed_at', 'paused_at', 'dropped_at'
            ])
            ->withTimestamps()
            ->wherePivot('status', '!=', 'dropped'); // Exclude dropped courses
    }

    public function completedCourses()
    {
        return $this->enrolledCourses()->wherePivot('status', 'completed');
    }

    public function activeCourses()
    {
        return $this->enrolledCourses()->wherePivot('status', 'active');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function activeCertificates()
    {
        return $this->certificates()->where('status', 'active');
    }

    /**
     * Get expired certificates
     */
    public function expiredCertificates()
    {
        return $this->certificates()->where('status', 'expired');
    }

    /**
     * Get revoked certificates
     */
    public function revokedCertificates()
    {
        return $this->certificates()->where('status', 'revoked');
    }

    /**
     * Get recent certificates (last 30 days)
     */
    public function recentCertificates()
    {
        return $this->certificates()
            ->where('issue_date', '>=', now()->subDays(30))
            ->orderBy('issue_date', 'desc');
    }

}
