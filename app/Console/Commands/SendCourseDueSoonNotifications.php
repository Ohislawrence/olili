<?php
// app/Console/Commands/SendCourseDueSoonNotifications.php

namespace App\Console\Commands;

use App\Services\CourseNotificationService;
use Illuminate\Console\Command;

class SendCourseDueSoonNotifications extends Command
{
    protected $signature = 'notifications:course-due-soon';
    protected $description = 'Send notifications for courses due soon';

    public function handle(CourseNotificationService $notificationService): int
    {
        $this->info('Checking for courses due soon...');

        $notificationService->checkAndSendDueSoonNotifications();

        $this->info('Due soon notifications processed successfully.');
        return Command::SUCCESS;
    }
}
