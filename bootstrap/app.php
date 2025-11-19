<?php

use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\TrackActiveSession::class,
        ]);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
     ->withSchedule(function (Schedule $schedule) {
        // Check for due soon courses daily at 9 AM
        $schedule->command('notifications:course-due-soon')
                 ->dailyAt('09:00')
                 ->onOneServer()
                 ->description('Send notifications for courses due soon')
                 ->withoutOverlapping(30); // Prevent overlapping runs

        // Check for overdue courses daily at 10 AM
        $schedule->command('notifications:course-overdue')
                 ->dailyAt('10:00')
                 ->onOneServer()
                 ->description('Send notifications for overdue courses')
                 ->withoutOverlapping(30);

        // Additional check for immediate notifications (runs every 6 hours)
        $schedule->command('notifications:course-overdue')
                 ->everySixHours()
                 ->onOneServer()
                 ->description('Quick check for overdue courses')
                 ->withoutOverlapping(10);

        // Immediate notifications (runs hourly during active hours)
        $schedule->command('notifications:immediate')
                ->hourly()
                ->between('8:00', '20:00')
                ->onOneServer()
                ->description('Send immediate notifications for urgent cases')
                ->withoutOverlapping(10);
        // Optional: Progress check for inactive students (runs daily at 11 AM)
        $schedule->command('notifications:check-inactive-students')
                 ->dailyAt('11:00')
                 ->onOneServer()
                 ->description('Check for inactive students and send reminders');
    })->create();
