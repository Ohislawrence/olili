<?php
// app/Listeners/SendLoginNotification.php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Notifications\LoginNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendLoginNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $user = $event->user;
        $request = request();

        // Get IP address and user agent
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Get location from IP (optional)
        $location = $this->getLocationFromIP($ipAddress);

        // Send notification
        $user->notify(new LoginNotification($ipAddress, $userAgent, $location));

        // Broadcast real-time notification
        $notificationData = [
            'title' => 'New Login Detected',
            'message' => 'Your account was accessed from ' . $this->getDeviceInfo($userAgent) . ' in ' . ($location ?? 'an unknown location'),
            'type' => 'security',
            'time' => now()->toISOString(),
        ];

        event(new UserLoggedIn($user, $notificationData));
    }

    /**
     * Get location information from IP address
     */
    protected function getLocationFromIP($ipAddress): ?string
    {
        try {
            // Using ipapi.co (free tier available)
            $response = Http::get("http://ipapi.co/{$ipAddress}/json/");

            if ($response->successful()) {
                $data = $response->json();

                $locationParts = [];
                if (!empty($data['city'])) {
                    $locationParts[] = $data['city'];
                }
                if (!empty($data['region'])) {
                    $locationParts[] = $data['region'];
                }
                if (!empty($data['country_name'])) {
                    $locationParts[] = $data['country_name'];
                }

                return implode(', ', $locationParts) ?: null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get location from IP: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Extract device information from user agent
     */
    protected function getDeviceInfo($userAgent): string
    {
        if (!$userAgent) {
            return 'Unknown Device';
        }

        $deviceInfo = 'Unknown Device';

        if (strpos($userAgent, 'Mobile') !== false) {
            $deviceInfo = 'Mobile Device';
        } elseif (strpos($userAgent, 'Tablet') !== false) {
            $deviceInfo = 'Tablet';
        } else {
            $deviceInfo = 'Desktop';
        }

        if (strpos($userAgent, 'Chrome') !== false) {
            $deviceInfo .= ' (Chrome)';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $deviceInfo .= ' (Firefox)';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $deviceInfo .= ' (Safari)';
        }

        return $deviceInfo;
    }
}
