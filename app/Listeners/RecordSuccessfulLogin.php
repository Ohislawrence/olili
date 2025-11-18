<?php
// app/Listeners/RecordSuccessfulLogin.php

namespace App\Listeners;

use App\Services\LoginTrackerService;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordSuccessfulLogin implements ShouldQueue
{
    protected $loginTracker;

    public function __construct(LoginTrackerService $loginTracker)
    {
        $this->loginTracker = $loginTracker;
    }

    public function handle(Login $event)
    {
        $user = $event->user;

        // Record the login
        $loginHistory = $this->loginTracker->recordSuccessfulLogin($user);

        // Check for suspicious activity
        $suspiciousActivities = $this->loginTracker->getSuspiciousActivity($user);

        if (!empty($suspiciousActivities)) {
            $this->loginTracker->sendSecurityAlert($user, $suspiciousActivities);
        }
    }
}
