<?php
// app/Listeners/RecordFailedLogin.php

namespace App\Listeners;

use App\Services\LoginTrackerService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordFailedLogin implements ShouldQueue
{
    protected $loginTracker;

    public function __construct(LoginTrackerService $loginTracker)
    {
        $this->loginTracker = $loginTracker;
    }

    public function handle(Failed $event)
    {
        if ($event->user) {
            $this->loginTracker->recordFailedLogin($event->user, 'Invalid credentials');
        }
    }
}
