<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\SendWeeklyProgressEmails;
use App\Console\Commands\CheckInactiveStudents;
use App\Console\Commands\CheckDueSoonCourses;
use App\Console\Commands\CheckOverdueCourses;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ================= DAILY NOTIFICATION SCHEDULE =================

// 8:00 AM - Check for courses due soon (gentle morning reminder)
Schedule::command(CheckDueSoonCourses::class)
    ->dailyAt('08:00')
    ->timezone(config('app.timezone', 'UTC'))
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Send notifications for courses due soon')
    ->appendOutputTo(storage_path('logs/due-soon.log'));

// 9:00 AM - Check for inactive students
Schedule::command(CheckInactiveStudents::class)
    ->dailyAt('09:00')
    ->timezone(config('app.timezone', 'UTC'))
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Check for inactive students and send reminders')
    ->appendOutputTo(storage_path('logs/inactive-students.log'));

// 10:00 AM - Check for overdue courses
Schedule::command(CheckOverdueCourses::class)
    ->dailyAt('10:00')
    ->timezone(config('app.timezone', 'UTC'))
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Check for overdue courses and send notifications')
    ->appendOutputTo(storage_path('logs/overdue-courses.log'));

// 2:00 PM - Afternoon reminder batch (for users in different timezones)
Schedule::command(CheckDueSoonCourses::class, ['--only-immediate'])
    ->dailyAt('14:00')
    ->timezone(config('app.timezone', 'UTC'))
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Afternoon due soon reminders')
    ->appendOutputTo(storage_path('logs/afternoon-reminders.log'));

// ================= WEEKLY EMAILS =================

// Send weekly progress emails every Monday at 8:00 AM UTC
Schedule::command(SendWeeklyProgressEmails::class)
    ->weekly()
    ->mondays()
    ->at('08:00')
    ->timezone('UTC')
    ->description('Send weekly progress emails to users')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/weekly-emails.log'));

// ================= SYSTEM MAINTENANCE =================

// Clean up old notifications every day at midnight
Schedule::command('notifications:process-scheduled')
    ->everyMinute()
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Process scheduled notifications')
    ->appendOutputTo(storage_path('logs/scheduled-notifications.log'));

// Clean up old database records daily at 3 AM
Schedule::command('model:prune')
    ->dailyAt('03:00')
    ->timezone('UTC')
    ->description('Clean up old database records')
    ->appendOutputTo(storage_path('logs/model-prune.log'));

// Clean up old course shares daily at 4 AM
Schedule::command('course-shares:cleanup')
    ->dailyAt('04:00')
    ->timezone('UTC')
    ->description('Clean up expired course shares')
    ->appendOutputTo(storage_path('logs/course-shares-cleanup.log'));

// ================= OPTIONAL: REPORTING =================

// Generate daily report at 11:30 PM
Schedule::command('reports:generate-daily')
    ->dailyAt('23:30')
    ->timezone('UTC')
    ->description('Generate daily activity report')
    ->appendOutputTo(storage_path('logs/daily-report.log'));

// ================= IMPORTANT: QUEUE WORKERS =================
// Process course generation queue every 5 minutes
Schedule::command('queue:work --queue=course_generation --stop-when-empty --sleep=3')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Process course generation queue')
    ->appendOutputTo(storage_path('logs/queue-course-generation.log'));

// Process regular queues every 5 minutes (emails first, then database, then default)
Schedule::command('queue:work --queue=emails,database,default --stop-when-empty --sleep=3')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->description('Process email and notification queues')
    ->appendOutputTo(storage_path('logs/queue-general.log'));

// Process failed jobs queue every 30 minutes (for retries)
Schedule::command('queue:retry all')
    ->everyThirtyMinutes()
    ->description('Retry failed queue jobs')
    ->appendOutputTo(storage_path('logs/queue-retry.log'));

// Prune failed jobs older than 7 days daily at 1 AM
Schedule::command('queue:prune-failed --hours=168')
    ->dailyAt('01:00')
    ->timezone('UTC')
    ->description('Clean up old failed jobs')
    ->appendOutputTo(storage_path('logs/queue-prune.log'));

// Flush failed jobs weekly (optional cleanup)
Schedule::command('queue:flush')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->timezone('UTC')
    ->description('Flush all failed jobs')
    ->appendOutputTo(storage_path('logs/queue-flush.log'));

// ================= HEALTH CHECKS =================

// Check system health every hour
Schedule::command('system:health-check')
    ->hourly()
    ->description('Run system health checks')
    ->appendOutputTo(storage_path('logs/health-check.log'));

// Backup database daily at 2 AM (if using spatie/laravel-backup)
Schedule::command('backup:run --only-db')
    ->dailyAt('02:00')
    ->timezone('UTC')
    ->description('Backup database')
    ->appendOutputTo(storage_path('logs/backup.log'));
