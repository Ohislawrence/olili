<?php
// app/Console/Commands/CheckOverdueCourses.php

namespace App\Console\Commands;

use App\Services\CourseNotificationService;
use Illuminate\Console\Command;

class CheckOverdueCourses extends Command
{
    protected $signature = 'notifications:check-overdue';

    protected $description = 'Check for overdue courses and send notifications';

    public function handle(CourseNotificationService $service): int
    {
        $this->info('Checking for overdue courses...');

        $service->checkAndSendOverdueNotifications();

        $this->info('Overdue notifications check completed.');
        return Command::SUCCESS;
    }
}
