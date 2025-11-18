<?php
// app/Providers/EventServiceProvider.php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use App\Listeners\RecordSuccessfulLogin;
use App\Listeners\RecordFailedLogin;
use App\Listeners\RecordLogout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            RecordSuccessfulLogin::class,
        ],
        Logout::class => [
            RecordLogout::class,
        ],
        Failed::class => [
            RecordFailedLogin::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
            SendWelcomeEmail::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
