<?php
// app/Console/Commands/SendImmediateCourseNotifications.php

namespace App\Console\Commands;

use App\Models\Course;
use App\Services\CourseNotificationService;
use Illuminate\Console\Command;

class SendImmediateCourseNotifications extends Command
{
    protected $signature = 'notifications:immediate';
    protected $description = 'Send immediate notifications for courses due today or just overdue';

    public function handle(CourseNotificationService $notificationService): int
    {
        $this->info('Checking for courses needing immediate notifications...');

        // Courses due today
        $coursesDueToday = Course::with(['studentProfile.user'])
            ->active()
            ->whereDate('target_completion_date', now()->toDateString())
            ->get();

        foreach ($coursesDueToday as $course) {
            $this->info("Sending due today notification for course: {$course->title}");
            $notificationService->sendDueSoonNotification($course, 0);
        }

        // Courses that became overdue today (yesterday was due date)
        $newlyOverdueCourses = Course::with(['studentProfile.user'])
            ->active()
            ->whereDate('target_completion_date', now()->subDay()->toDateString())
            ->get();

        foreach ($newlyOverdueCourses as $course) {
            $this->info("Sending newly overdue notification for course: {$course->title}");
            $notificationService->sendImmediateOverdueNotification($course);
        }

        $totalProcessed = $coursesDueToday->count() + $newlyOverdueCourses->count();
        $this->info("Sent {$totalProcessed} immediate notifications.");

        return Command::SUCCESS;
    }
}
