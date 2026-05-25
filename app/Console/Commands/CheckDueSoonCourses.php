<?php
// app/Console/Commands/CheckDueSoonCourses.php

namespace App\Console\Commands;

use App\Services\CourseNotificationService;
use Illuminate\Console\Command;

class CheckDueSoonCourses extends Command
{
    protected $signature = 'notifications:check-due-soon
                            {--only-immediate : Only check courses due today/tomorrow}';

    protected $description = 'Check for courses due soon and send notifications';

    public function handle(CourseNotificationService $service): int
    {
        $this->info('Checking for courses due soon...');

        if ($this->option('only-immediate')) {
            // Only check courses due today or tomorrow
            $service->checkAndSendImmediateDueSoonNotifications();
        } else {
            // Check all courses due soon (up to 7 days)
            $service->checkAndSendDueSoonNotifications();
        }

        $this->info('Due soon notifications check completed.');
        return Command::SUCCESS;
    }
}
