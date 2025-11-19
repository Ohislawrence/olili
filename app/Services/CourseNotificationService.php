<?php
// app/Services/CourseNotificationService.php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Notifications\CourseDueSoonNotification;
use App\Notifications\CourseOverdueNotification;
use App\Notifications\InactiveStudentNotification;
use Illuminate\Support\Facades\Notification;

class CourseNotificationService
{
    public function checkAndSendDueSoonNotifications(): void
    {
        $dueSoonCourses = Course::with(['studentProfile.user'])
            ->active()
            ->where('target_completion_date', '<=', now()->addDays(7))
            ->where('target_completion_date', '>', now())
            ->whereDoesntHave('studentProfile.user.notifications', function ($query) {
                $query->where('data->type', 'course_due_soon')
                      ->where('created_at', '>', now()->subDays(1));
            })
            ->get();

        $this->info("Found {$dueSoonCourses->count()} courses due soon.");

        foreach ($dueSoonCourses as $course) {
            $daysRemaining = now()->diffInDays($course->target_completion_date);

            // Send notification at specific intervals
            if (in_array($daysRemaining, [7, 3, 1])) {
                $this->sendDueSoonNotification($course, $daysRemaining);
            }
        }
    }

    public function checkAndSendOverdueNotifications(): void
    {
        $overdueCourses = Course::with(['studentProfile.user'])
            ->active()
            ->where('target_completion_date', '<', now())
            ->whereDoesntHave('studentProfile.user.notifications', function ($query) {
                $query->where('data->type', 'course_overdue')
                      ->where('created_at', '>', now()->subDays(3));
            })
            ->get();

        $this->info("Found {$overdueCourses->count()} overdue courses.");

        foreach ($overdueCourses as $course) {
            $daysOverdue = now()->diffInDays($course->target_completion_date);

            // Send notifications periodically for 30 days after due date
            if ($daysOverdue <= 30) {
                $this->sendPeriodicOverdueNotification($course, $daysOverdue);
            }
        }
    }

    public function sendDueSoonNotification(Course $course, int $daysRemaining): void
    {
        $user = $course->studentProfile->user;

        try {
            $user->notify(new CourseDueSoonNotification($course, $daysRemaining));
            $this->info("Sent due soon notification for course: {$course->title} ({$daysRemaining} days remaining)");
        } catch (\Exception $e) {
            $this->error("Failed to send notification for course {$course->title}: " . $e->getMessage());
        }
    }

    public function sendImmediateOverdueNotification(Course $course): void
    {
        $user = $course->studentProfile->user;
        $daysOverdue = now()->diffInDays($course->target_completion_date);

        try {
            $user->notify(new CourseOverdueNotification($course, $daysOverdue));
            $this->info("Sent immediate overdue notification for course: {$course->title} ({$daysOverdue} days overdue)");
        } catch (\Exception $e) {
            $this->error("Failed to send immediate overdue notification for course {$course->title}: " . $e->getMessage());
        }
    }

    private function sendPeriodicOverdueNotification(Course $course, int $daysOverdue): void
    {
        $user = $course->studentProfile->user;

        // Define notification intervals (days overdue)
        $notificationIntervals = [1, 3, 7, 14, 21, 30];

        if (in_array($daysOverdue, $notificationIntervals)) {
            try {
                $user->notify(new CourseOverdueNotification($course, $daysOverdue));
                $this->info("Sent periodic overdue notification for course: {$course->title} ({$daysOverdue} days overdue)");
            } catch (\Exception $e) {
                $this->error("Failed to send periodic overdue notification for course {$course->title}: " . $e->getMessage());
            }
        }
    }

    public function sendInactivityNotification(Course $course, int $daysInactive): void
    {
        $user = $course->studentProfile->user;

        try {
            $user->notify(new InactiveStudentNotification($course, $daysInactive));
            $this->info("Sent inactivity notification for course: {$course->title} ({$daysInactive} days inactive)");
        } catch (\Exception $e) {
            $this->error("Failed to send inactivity notification for course {$course->title}: " . $e->getMessage());
        }
    }

    // Helper method to check if a course needs immediate notification
    public function checkCourseForImmediateNotification(Course $course): bool
    {
        if ($course->status !== 'active') {
            return false;
        }

        // Check if course is due today
        if ($course->target_completion_date->isToday()) {
            $this->sendDueSoonNotification($course, 0);
            return true;
        }

        // Check if course is overdue
        if ($course->target_completion_date->isPast()) {
            $daysOverdue = now()->diffInDays($course->target_completion_date);
            if ($daysOverdue === 1) { // First day overdue
                $this->sendImmediateOverdueNotification($course);
                return true;
            }
        }

        return false;
    }

    // Helper methods for logging
    private function info(string $message): void
    {
        if (app()->runningInConsole()) {
            // This will be shown when running as artisan command
            echo $message . PHP_EOL;
        }
        \Log::info($message);
    }

    private function error(string $message): void
    {
        if (app()->runningInConsole()) {
            // This will be shown when running as artisan command
            echo "ERROR: " . $message . PHP_EOL;
        }
        \Log::error($message);
    }
}
