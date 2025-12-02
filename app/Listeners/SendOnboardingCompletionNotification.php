<?php
namespace App\Listeners;

use App\Events\OnboardingCompleted;
use App\Notifications\OnboardingCompletedNotification;

class SendOnboardingCompletionNotification
{
    public function handle(OnboardingCompleted $event): void
    {
        $event->user->notify(new OnboardingCompletedNotification());
    }
}