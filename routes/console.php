<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\SendInactivityReminders;
use App\Console\Commands\SendCompletionReminders;
use App\Console\Commands\SendAllReminders;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// ================= NOTIFICATION COMMANDS =================
// Morning notification batch
// ================= REMINDER COMMANDS =================

// Morning notification batch
Schedule::command('reminders:completion')
    ->dailyAt('08:00')
    ->timezone(config('app.timezone', 'UTC'))
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Send notifications for courses due soon');

Schedule::command('reminders:inactivity')
    ->dailyAt('09:00')
    ->timezone(config('app.timezone', 'UTC'))
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Check for inactive students and send reminders');

Schedule::command('queue:work --queue=course_generation --stop-when-empty --sleep=3')
    ->everyFiveMinutes()
    ->withoutOverlapping();


Schedule::command('queue:work --queue=emails,database,default --stop-when-empty --sleep=3')
    ->everyFiveMinutes()
    ->withoutOverlapping();


// ================= SYSTEM MAINTENANCE =================


// Optional: Cleanup tasks
Schedule::command('model:prune')
    ->daily()
    ->description('Clean up old database records');

Schedule::command('notifications:process-scheduled')
        ->everyMinute()
        ->withoutOverlapping();

Schedule::command('course-shares:cleanup')->daily();
