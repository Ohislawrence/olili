<?php
// app/Services/LoginTrackerService.php

namespace App\Services;

use App\Models\User;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LoginTrackerService
{
    public function recordSuccessfulLogin(User $user, string $loginType = 'password'): LoginHistory
    {
        return $user->recordLogin($loginType, true);
    }

    public function recordFailedLogin(User $user, string $failureReason = 'Invalid credentials'): LoginHistory
    {
        return $user->recordLogin('password', false, $failureReason);
    }

    public function recordLogout(User $user): void
    {
        $user->recordLogout();
    }

    public function getSuspiciousActivity(User $user, int $hours = 24): array
    {
        $recentLogins = $user->loginHistories()
            ->where('login_at', '>=', now()->subHours($hours))
            ->get();

        $suspiciousActivities = [];

        // Check for multiple IP addresses in short time
        $uniqueIps = $recentLogins->pluck('ip_address')->unique()->count();
        if ($uniqueIps > 3) {
            $suspiciousActivities[] = "Multiple IP addresses ({$uniqueIps}) detected within {$hours} hours";
        }

        // Check for failed login attempts
        $failedAttempts = $recentLogins->where('was_successful', false)->count();
        if ($failedAttempts > 2) {
            $suspiciousActivities[] = "Multiple failed login attempts ({$failedAttempts})";
        }

        // Check for unusual locations (simplified check)
        $locations = $recentLogins->pluck('country')->filter()->unique()->count();
        if ($locations > 2) {
            $suspiciousActivities[] = "Logins from multiple countries ({$locations})";
        }

        return $suspiciousActivities;
    }

    public function getUserLoginStats(User $user): array
    {
        $cacheKey = "user_login_stats_{$user->id}";

        return Cache::remember($cacheKey, 3600, function () use ($user) {
            $thirtyDaysAgo = now()->subDays(30);

            $recentLogins = $user->loginHistories()
                ->where('login_at', '>=', $thirtyDaysAgo)
                ->get();

            $successfulLogins = $recentLogins->where('was_successful', true);
            $failedLogins = $recentLogins->where('was_successful', false);

            return [
                'total_logins_30d' => $successfulLogins->count(),
                'failed_logins_30d' => $failedLogins->count(),
                'success_rate_30d' => $recentLogins->count() > 0
                    ? round(($successfulLogins->count() / $recentLogins->count()) * 100, 1)
                    : 0,
                'unique_devices' => $recentLogins->pluck('device_type')->unique()->count(),
                'unique_locations' => $recentLogins->pluck('country')->filter()->unique()->count(),
                'average_session_duration' => $this->calculateAverageSessionDuration($successfulLogins),
                'most_common_device' => $this->getMostCommonValue($recentLogins, 'device_type'),
                'most_common_browser' => $this->getMostCommonValue($recentLogins, 'browser'),
                'consecutive_days' => $this->calculateConsecutiveDays($user),
            ];
        });
    }

    public function getPlatformLoginStats($days = 30): array
    {
        $startDate = now()->subDays($days);

        $logins = LoginHistory::where('login_at', '>=', $startDate)
            ->where('was_successful', true)
            ->get();

        return [
            'platforms' => $logins->groupBy('platform')->map->count(),
            'browsers' => $logins->groupBy('browser')->map->count(),
            'devices' => $logins->groupBy('device_type')->map->count(),
            'login_times' => $logins->groupBy(function ($login) {
                return $login->login_at->hour;
            })->map->count(),
        ];
    }

    protected function calculateAverageSessionDuration($logins): ?int
    {
        $sessionsWithDuration = $logins->filter(function ($login) {
            return !is_null($login->session_duration_seconds);
        });

        if ($sessionsWithDuration->isEmpty()) {
            return null;
        }

        return (int) $sessionsWithDuration->avg('session_duration_seconds');
    }

    protected function getMostCommonValue($collection, string $field): ?string
    {
        $values = $collection->pluck($field)->filter()->countBy();

        if ($values->isEmpty()) {
            return null;
        }

        return $values->sortDesc()->keys()->first();
    }

    public function sendSecurityAlert(User $user, array $suspiciousActivities): void
    {
        // Here you can implement email notifications, push notifications, etc.
        Log::warning("Security alert for user {$user->id}: " . implode(', ', $suspiciousActivities));

        // Example email notification (uncomment if you have email setup)
        // Mail::to($user->email)->send(new SecurityAlertMail($user, $suspiciousActivities));
    }

    private function calculateConsecutiveDays(User $user)
    {
        $logins = $user->loginHistories()
            ->where('was_successful', true)
            ->orderBy('login_at', 'desc')
            ->get();

        if ($logins->isEmpty()) {
            return 0;
        }

        $consecutive = 1;
        $currentDate = $logins->first()->login_at->startOfDay();

        foreach ($logins->skip(1) as $login) {
            $loginDate = $login->login_at->startOfDay();
            $diffInDays = $currentDate->diffInDays($loginDate);

            if ($diffInDays === 1) {
                $consecutive++;
                $currentDate = $loginDate;
            } else if ($diffInDays > 1) {
                break;
            }
            // If diffInDays === 0, it's the same day, so we skip
        }

        return $consecutive;
    }
}
