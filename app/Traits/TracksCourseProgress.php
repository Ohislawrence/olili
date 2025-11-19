<?php
// app/Traits/TracksCourseProgress.php

namespace App\Traits;

use App\Models\Course;
use App\Services\CourseNotificationService;

trait TracksCourseProgress
{
    public function checkCourseProgressAndNotify(Course $course): void
    {
        $lastActivity = $course->updated_at;
        $daysSinceActivity = now()->diffInDays($lastActivity);

        // If no activity for 3 days and course is due soon or overdue
        if ($daysSinceActivity >= 3 && $course->status === 'active') {
            $notificationService = app(CourseNotificationService::class);

            if ($course->target_completion_date->isFuture()) {
                $daysRemaining = now()->diffInDays($course->target_completion_date);
                if ($daysRemaining <= 7) {
                    $notificationService->sendDueSoonNotification($course, $daysRemaining);
                }
            } else {
                $notificationService->sendImmediateOverdueNotification($course);
            }
        }
    }
}
