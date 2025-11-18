<?php
// app/Listeners/LoginEventListener.php

namespace App\Listeners;

use App\Services\LoginTrackerService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;

class LoginEventListener
{
    public function __construct(
        private LoginTrackerService $loginTracker
    ) {}

    public function handleLogin(Login $event): void
    {
        $this->loginTracker->recordSuccessfulLogin($event->user);

        // Check for suspicious activity
        $suspiciousActivities = $this->loginTracker->getSuspiciousActivity($event->user);

        if (!empty($suspiciousActivities)) {
            $this->loginTracker->sendSecurityAlert($event->user, $suspiciousActivities);
        }
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            $this->loginTracker->recordLogout($event->user);
        }
    }

    public function handleFailedLogin(Failed $event): void
    {
        if ($event->user) {
            $this->loginTracker->recordFailedLogin($event->user, 'Invalid credentials');
        }
    }
}
