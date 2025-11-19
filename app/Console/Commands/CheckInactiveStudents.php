<?php
// app/Console/Commands/CheckInactiveStudents.php

namespace App\Console\Commands;

use App\Models\Course;
use App\Services\CourseNotificationService;
use Illuminate\Console\Command;

class CheckInactiveStudents extends Command
{
    protected $signature = 'notifications:check-inactive-students';
    protected $description = 'Check for inactive students and send reminders';

    public function handle(CourseNotificationService $notificationService): int
    {
        $this->info('Checking for inactive students...');

        $inactiveCourses = Course::with(['studentProfile.user'])
            ->active()
            ->where('updated_at', '<', now()->subDays(3)) // No activity for 3 days
            ->get();

        $processedCount = 0;

        foreach ($inactiveCourses as $course) {
            $daysInactive = now()->diffInDays($course->updated_at);

            // Check if course is due soon or overdue
            if ($course->target_completion_date->isFuture()) {
                $daysRemaining = now()->diffInDays($course->target_completion_date);
                if ($daysRemaining <= 7) {
                    $this->info("Sending inactivity reminder for course: {$course->title}");
                    $notificationService->sendDueSoonNotification($course, $daysRemaining);
                    $processedCount++;
                }
            } else {
                $this->info("Sending overdue reminder for inactive course: {$course->title}");
                $notificationService->sendImmediateOverdueNotification($course);
                $processedCount++;
            }
        }

        $this->info("Processed {$processedCount} inactive courses.");
        return Command::SUCCESS;
    }
}
