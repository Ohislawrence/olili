<?php
// app/Console/Commands/SendCourseOverdueNotifications.php

namespace App\Console\Commands;

use App\Services\CourseNotificationService;
use Illuminate\Console\Command;

class SendCourseOverdueNotifications extends Command
{
    protected $signature = 'notifications:course-overdue';
    protected $description = 'Send notifications for overdue courses';

    public function handle(CourseNotificationService $notificationService): int
    {
        $this->info('Checking for overdue courses...');

        $notificationService->checkAndSendOverdueNotifications();

        $this->info('Overdue notifications processed successfully.');
        return Command::SUCCESS;
    }
}
