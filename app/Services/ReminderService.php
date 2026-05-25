<?php

namespace App\Services;

use App\Models\CourseEnrollment;
use App\Models\ProgressTracking;
use App\Notifications\InactivityReminder;
use App\Notifications\CourseCompletionReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReminderService
{
    /**
     * Send inactivity reminders for all active enrollments
     */
    public function sendInactivityReminders()
    {
        $inactivityThresholds = [2, 3, 4, 7, 8]; // Days

        $activeEnrollments = CourseEnrollment::with(['course', 'user'])
            ->whereIn('status', ['enrolled', 'active'])
            ->whereNotNull('started_at')
            ->get();

        foreach ($activeEnrollments as $enrollment) {
            $lastActivity = $this->getLastActivityDate($enrollment->user_id, $enrollment->course_id);

            if (!$lastActivity) {
                // If no activity recorded, use enrollment start date
                $lastActivity = $enrollment->started_at ?? $enrollment->enrolled_at;
            }

            if (!$lastActivity) continue;

            $inactiveDays = Carbon::now()->diffInDays($lastActivity);

            // Check if this inactivity period matches any threshold
            foreach ($inactivityThresholds as $days) {
                if ($inactiveDays === $days) {
                    // Check if reminder already sent for this threshold
                    if (!$this->alreadyNotified($enrollment->user_id, 'inactivity', $days, $enrollment->course_id)) {
                        $enrollment->user->notify(
                            new InactivityReminder($enrollment, $days, $lastActivity)
                        );
                        break; // Only send one notification per check
                    }
                }
            }
        }
    }

    /**
     * Send course completion reminders
     */
    public function sendCompletionReminders()
    {
        $reminderDays = [14, 7, 3, 1]; // Days before completion
        $overdueDays = [1, 3, 7]; // Days after completion date

        $activeEnrollments = CourseEnrollment::with(['course', 'user'])
            ->whereIn('status', ['enrolled', 'active'])
            ->whereNotNull('est_completion_time')
            ->get();

        foreach ($activeEnrollments as $enrollment) {
            $completionDate = Carbon::parse($enrollment->est_completion_time);
            $daysRemaining = Carbon::now()->diffInDays($completionDate, false); // Negative if overdue

            if ($daysRemaining > 0) {
                // Check upcoming completion
                foreach ($reminderDays as $days) {
                    if ($daysRemaining === $days) {
                        if (!$this->alreadyNotified($enrollment->user_id, 'completion', $days, $enrollment->course_id)) {
                            $enrollment->user->notify(
                                new CourseCompletionReminder($enrollment, $days)
                            );
                            break;
                        }
                    }
                }
            } else {
                // Check overdue completion
                $daysOverdue = abs($daysRemaining);
                foreach ($overdueDays as $days) {
                    if ($daysOverdue === $days) {
                        if (!$this->alreadyNotified($enrollment->user_id, 'overdue', $days, $enrollment->course_id)) {
                            $enrollment->user->notify(
                                new CourseCompletionReminder($enrollment, $daysOverdue, true)
                            );
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Get last activity date for a user in a course
     */
    protected function getLastActivityDate($userId, $courseId): ?Carbon
    {
        $lastActivity = ProgressTracking::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastActivity ? Carbon::parse($lastActivity->created_at) : null;
    }

    /**
     * Check if notification already sent for this specific reminder
     */
    protected function alreadyNotified($userId, $type, $days, $courseId): bool
    {
        return DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('notifiable_type', 'App\Models\User')
            ->whereJsonContains('data->type', $type . '_reminder')
            ->whereJsonContains('data->course_id', $courseId)
            ->where(function($query) use ($type, $days) {
                if ($type === 'inactivity') {
                    $query->whereJsonContains('data->inactive_days', $days);
                } elseif ($type === 'completion' || $type === 'overdue') {
                    $query->whereJsonContains('data->days_remaining', $days);
                }
            })
            ->where('created_at', '>=', Carbon::now()->subDays(1))
            ->exists();
    }

    /**
     * Run all reminder checks
     */
    public function sendAllReminders()
    {
        $this->sendInactivityReminders();
        $this->sendCompletionReminders();
    }
}
