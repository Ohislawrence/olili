<?php
// app/Console/Commands/CheckInactiveStudents.php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\CourseEnrollment;
use App\Services\CourseNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckInactiveStudents extends Command
{
    protected $signature = 'notifications:check-inactive-students
                            {--days=3 : Number of days to consider inactive}';

    protected $description = 'Check for inactive students and send reminders';

    public function handle(CourseNotificationService $notificationService): int
    {
        $inactiveDays = (int) $this->option('days');
        $this->info("Checking for students inactive for {$inactiveDays} days...");

        // Get users with active enrollments who haven't been active
        $inactiveEnrollments = CourseEnrollment::with([
                'user',
                'course',
                'user.studentProfile'
            ])
            ->whereIn('status', ['active', 'enrolled'])
            ->where('last_accessed_at', '<', now()->subDays($inactiveDays))
            ->orWhere(function ($query) use ($inactiveDays) {
                $query->whereNull('last_accessed_at')
                      ->where('enrolled_at', '<', now()->subDays($inactiveDays));
            })
            ->get();

        $processedCount = 0;
        $sentReminders = [];

        foreach ($inactiveEnrollments as $enrollment) {
            $user = $enrollment->user;
            $course = $enrollment->course;

            if (!$user || !$course) {
                continue;
            }

            // Calculate days inactive
            $lastActive = $enrollment->last_accessed_at ?? $enrollment->enrolled_at;
            $daysInactive = now()->diffInDays($lastActive);

            // Skip if we already sent a reminder to this user today
            $cacheKey = "inactive_reminder_{$user->id}_{$course->id}";
            if (cache()->has($cacheKey)) {
                continue;
            }

            $this->info("User {$user->name} (ID: {$user->id}) inactive for {$daysInactive} days in course: {$course->title}");

            // Check course completion status
            $daysRemaining = null;
            if ($course->target_completion_date && $course->target_completion_date->isFuture()) {
                $daysRemaining = now()->diffInDays($course->target_completion_date);
            }

            // Send appropriate notification
            try {
                if ($daysRemaining !== null && $daysRemaining <= 7) {
                    $this->info("  → Course due in {$daysRemaining} days, sending reminder");
                    $notificationService->sendDueSoonNotification($enrollment, $daysRemaining);
                } elseif ($daysRemaining !== null && $daysRemaining <= 0) {
                    $this->info("  → Course is overdue, sending notification");
                    $notificationService->sendImmediateOverdueNotification($enrollment);
                } else {
                    $this->info("  → Sending general inactivity reminder");
                    $notificationService->sendInactivityReminder($enrollment, $daysInactive);
                }

                // Mark as sent to prevent duplicate notifications today
                cache()->put($cacheKey, true, now()->addDay());
                $processedCount++;

                // Record the notification
                $sentReminders[] = [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'days_inactive' => $daysInactive,
                    'days_remaining' => $daysRemaining,
                    'sent_at' => now(),
                ];

            } catch (\Exception $e) {
                $this->error("Failed to send notification for user {$user->id}, course {$course->id}: " . $e->getMessage());
                \Log::error("Inactive student notification error", [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Log summary
        $this->info("Processed {$processedCount} inactive students.");

        // Log to database for reporting
        if (!empty($sentReminders)) {
            DB::table('inactivity_reminders_log')->insert($sentReminders);
        }

        return Command::SUCCESS;
    }
}
