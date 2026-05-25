<?php
// app/Services/CourseNotificationService.php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Notifications\CourseDueSoonNotification;
use App\Notifications\CourseOverdueNotification;
use App\Notifications\InactiveStudentNotification;
use App\Mail\InactivityReminderEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class CourseNotificationService
{
    /**
     * Send inactivity reminder for a course enrollment
     */
    public function sendInactivityReminder(CourseEnrollment $enrollment, int $daysInactive): void
    {
        $user = $enrollment->user;
        $course = $enrollment->course;

        if (!$user || !$course) {
            Log::warning('Cannot send inactivity reminder - missing user or course', [
                'enrollment_id' => $enrollment->id
            ]);
            return;
        }

        try {
            // Send email
            Mail::to($user->email)->queue(new InactivityReminderEmail(
                $user,
                $course,
                $daysInactive,
                $enrollment->progress_percentage
            ));

            // Send notification
            $user->notify(new InactiveStudentNotification($course, $daysInactive));

            // Log the notification
            Log::info("Inactivity reminder sent", [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'days_inactive' => $daysInactive,
                'progress' => $enrollment->progress_percentage
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send inactivity reminder", [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send due soon notification for a course
     */
    public function sendDueSoonNotification(CourseEnrollment $enrollment, int $daysRemaining): void
    {
        $user = $enrollment->user;
        $course = $enrollment->course;

        if (!$user || !$course) {
            Log::warning('Cannot send due soon notification - missing user or course', [
                'enrollment_id' => $enrollment->id
            ]);
            return;
        }

        try {
            $user->notify(new CourseDueSoonNotification($course, $daysRemaining));

            Log::info("Sent due soon notification", [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'days_remaining' => $daysRemaining
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send due soon notification", [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send immediate overdue notification
     */
    public function sendImmediateOverdueNotification(CourseEnrollment $enrollment): void
    {
        $user = $enrollment->user;
        $course = $enrollment->course;

        if (!$user || !$course) {
            Log::warning('Cannot send overdue notification - missing user or course', [
                'enrollment_id' => $enrollment->id
            ]);
            return;
        }

        $daysOverdue = now()->diffInDays($course->target_completion_date, false) * -1;

        try {
            $user->notify(new CourseOverdueNotification($course, $daysOverdue));

            Log::info("Sent immediate overdue notification", [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'days_overdue' => $daysOverdue
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send overdue notification", [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send periodic overdue notification based on intervals
     */
    private function sendPeriodicOverdueNotification(CourseEnrollment $enrollment, int $daysOverdue): void
    {
        $user = $enrollment->user;
        $course = $enrollment->course;

        if (!$user || !$course) {
            return;
        }

        // Define notification intervals (days overdue)
        $notificationIntervals = [1, 3, 7, 14, 21, 30];

        if (in_array($daysOverdue, $notificationIntervals)) {
            try {
                $user->notify(new CourseOverdueNotification($course, $daysOverdue));

                Log::info("Sent periodic overdue notification", [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'enrollment_id' => $enrollment->id,
                    'days_overdue' => $daysOverdue
                ]);

            } catch (\Exception $e) {
                Log::error("Failed to send periodic overdue notification", [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Check and send due soon notifications for all courses
     */
    public function checkAndSendDueSoonNotifications(): void
    {
        // Get active enrollments where course is due soon
        $dueSoonEnrollments = CourseEnrollment::with(['user', 'course'])
            ->whereIn('status', ['active', 'enrolled'])
            ->whereHas('course', function ($query) {
                $query->where('status', 'active')
                      ->where('target_completion_date', '<=', now()->addDays(7))
                      ->where('target_completion_date', '>=', now());
            })
            ->get();

        Log::info("Found {$dueSoonEnrollments->count()} course enrollments due soon.");

        foreach ($dueSoonEnrollments as $enrollment) {
            $course = $enrollment->course;
            $daysRemaining = now()->diffInDays($course->target_completion_date, false);

            // Check if notification should be sent today
            if (in_array($daysRemaining, [7, 3, 1, 0])) {
                // Check if user has been notified today
                if (!$this->hasUserBeenNotifiedToday($enrollment->user, 'course_due_soon', $course->id)) {
                    $this->sendDueSoonNotification($enrollment, $daysRemaining);
                }
            }
        }
    }

    /**
     * Check and send overdue notifications for all courses
     */
    public function checkAndSendOverdueNotifications(): void
    {
        // Get active enrollments where course is overdue
        $overdueEnrollments = CourseEnrollment::with(['user', 'course'])
            ->whereIn('status', ['active', 'enrolled'])
            ->whereHas('course', function ($query) {
                $query->where('status', 'active')
                      ->where('target_completion_date', '<', now());
            })
            ->get();

        Log::info("Found {$overdueEnrollments->count()} overdue course enrollments.");

        foreach ($overdueEnrollments as $enrollment) {
            $course = $enrollment->course;
            $daysOverdue = now()->diffInDays($course->target_completion_date) * -1;

            // Check if it's the first day of being overdue
            if ($daysOverdue === 1) {
                // Check if already notified today
                if (!$this->hasUserBeenNotifiedToday($enrollment->user, 'course_overdue', $course->id)) {
                    $this->sendImmediateOverdueNotification($enrollment);
                }
            } else if ($daysOverdue <= 30) {
                // Send periodic notifications for up to 30 days
                $this->sendPeriodicOverdueNotification($enrollment, $daysOverdue);
            }
        }
    }

    /**
     * Send inactivity notification for a course
     */
    public function sendInactivityNotification(Course $course, int $daysInactive): void
    {
        // This method should use enrollments, not direct course-user relationship
        $enrollments = $course->enrollments()
            ->with('user')
            ->whereIn('status', ['active', 'enrolled'])
            ->get();

        foreach ($enrollments as $enrollment) {
            $this->sendInactivityReminder($enrollment, $daysInactive);
        }
    }

    /**
     * Check if a user has been notified about a specific course today
     */
    private function hasUserBeenNotifiedToday(?User $user, string $type, int $courseId): bool
    {
        if (!$user) {
            return false;
        }

        return $user->notifications()
            ->where('data->type', $type)
            ->where('data->course_id', $courseId)
            ->whereDate('created_at', today())
            ->exists();
    }

    /**
     * Check if a course needs immediate notification
     */
    public function checkCourseForImmediateNotification(Course $course): bool
    {
        if ($course->status !== 'active') {
            return false;
        }

        $hasSent = false;

        // Get all active enrollments for this course
        $enrollments = $course->enrollments()
            ->with('user')
            ->whereIn('status', ['active', 'enrolled'])
            ->get();

        foreach ($enrollments as $enrollment) {
            $user = $enrollment->user;

            if (!$user) {
                continue;
            }

            // Check if course is due today
            if ($course->target_completion_date->isToday()) {
                if (!$this->hasUserBeenNotifiedToday($user, 'course_due_soon', $course->id)) {
                    $this->sendDueSoonNotification($enrollment, 0);
                    $hasSent = true;
                }
            }

            // Check if course became overdue today (yesterday was due date)
            if ($course->target_completion_date->isYesterday()) {
                if (!$this->hasUserBeenNotifiedToday($user, 'course_overdue', $course->id)) {
                    $this->sendImmediateOverdueNotification($enrollment);
                    $hasSent = true;
                }
            }
        }

        return $hasSent;
    }

    /**
     * Helper method for console output
     */
    private function info(string $message): void
    {
        if (app()->runningInConsole()) {
            echo $message . PHP_EOL;
        }
        Log::info($message);
    }

    /**
     * Helper method for console error output
     */
    private function error(string $message): void
    {
        if (app()->runningInConsole()) {
            echo "ERROR: " . $message . PHP_EOL;
        }
        Log::error($message);
    }

    public function checkAndSendImmediateDueSoonNotifications(): void
    {
        // Get enrollments where course is due today or tomorrow
        $immediateDueEnrollments = CourseEnrollment::with(['user', 'course'])
            ->whereIn('status', ['active', 'enrolled'])
            ->whereHas('course', function ($query) {
                $query->where('status', 'active')
                    ->whereDate('target_completion_date', '<=', now()->addDay())
                    ->whereDate('target_completion_date', '>=', now());
            })
            ->get();

        Log::info("Found {$immediateDueEnrollments->count()} course enrollments due immediately.");

        foreach ($immediateDueEnrollments as $enrollment) {
            $course = $enrollment->course;
            $daysRemaining = now()->diffInDays($course->target_completion_date, false);

            if (!$this->hasUserBeenNotifiedToday($enrollment->user, 'course_due_soon', $course->id)) {
                $this->sendDueSoonNotification($enrollment, $daysRemaining);
            }
        }
    }
}
