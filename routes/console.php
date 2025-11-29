<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//Schedule::command('reminders:send-course-creation')
//    ->hourly()
//    ->withoutOverlapping()
 //   ->description('Send course creation reminders to users without courses'); 

// ================= NOTIFICATION COMMANDS =================
// Morning notification batch
Schedule::command('notifications:course-due-soon')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->description('Send notifications for courses due soon');

Schedule::command('notifications:check-inactive-students')
    ->dailyAt('09:00')
    ->withoutOverlapping()
    ->description('Check for inactive students and send reminders');

Schedule::command('notifications:course-overdue')
    ->dailyAt('10:00')
    ->withoutOverlapping()
    ->description('Send notifications for overdue courses');

Schedule::command('notifications:immediate')
    ->dailyAt('10:00')
    ->withoutOverlapping()
    ->description('Checking for courses needing immediate notifications...');

// Course creation reminders (more frequent)
Schedule::command('reminders:send-course-creation')
    ->everySixHours()
    ->withoutOverlapping()
    ->description('Send course creation reminders to users without courses');

// ================= SYSTEM MAINTENANCE =================
// Queue processing
Schedule::command('queue:work --stop-when-empty --max-time=300')
    ->everyMinute()
    ->withoutOverlapping()
    ->description('Process email queue jobs');

// Optional: Cleanup tasks
Schedule::command('model:prune')
    ->daily()
    ->description('Clean up old database records');
