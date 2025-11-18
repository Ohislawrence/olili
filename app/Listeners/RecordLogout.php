<?php
// app/Listeners/RecordLogout.php

namespace App\Listeners;

use App\Services\LoginTrackerService;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordLogout implements ShouldQueue
{
    protected $loginTracker;

    public function __construct(LoginTrackerService $loginTracker)
    {
        $this->loginTracker = $loginTracker;
    }

    public function handle(Logout $event)
    {
        $this->loginTracker->recordLogout($event->user);
    }
}
