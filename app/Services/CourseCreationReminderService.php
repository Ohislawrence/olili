<?php
// app/Services/CourseCreationReminderService.php
namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseCreationReminder;
use App\Notifications\CourseCreationReminder as ReminderNotification;
use Illuminate\Support\Facades\DB;

class CourseCreationReminderService
{
    public function getUsersWithoutCourses()
    {
        // Get users who are students and have no courses
        return User::role('student')
            ->whereDoesntHave('courses')
            ->where('is_active', true)
            ->get();
    }

    public function sendReminders()
    {
        $users = $this->getUsersWithoutCourses();
        $sentCount = 0;

        foreach ($users as $user) {
            if ($this->sendReminderToUser($user)) {
                $sentCount++;
            }
        }

        return $sentCount;
    }

    public function sendReminderToUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            // Get or create reminder record
            $reminder = CourseCreationReminder::firstOrCreate(
                ['user_id' => $user->id],
                ['reminder_count' => 0]
            );

            // Check if we can send reminder
            if (!$reminder->canSendReminder()) {
                return false;
            }

            try {
                // Send notification
                $user->notify(new ReminderNotification($reminder->reminder_count));
                
                // Update reminder record
                $reminder->markReminderSent();
                
                \Log::info("Course creation reminder sent to user {$user->id}, count: {$reminder->reminder_count}");
                
                return true;
            } catch (\Exception $e) {
                \Log::error("Failed to send course creation reminder to user {$user->id}: " . $e->getMessage());
                return false;
            }
        });
    }

    public function markUserAsCompleted(User $user): void
    {
        CourseCreationReminder::updateOrCreate(
            ['user_id' => $user->id],
            ['completed_at' => now()]
        );
    }

    public function getUserReminderStats(User $user): ?array
    {
        $reminder = CourseCreationReminder::where('user_id', $user->id)->first();
        
        if (!$reminder) {
            return null;
        }

        return [
            'reminder_count' => $reminder->reminder_count,
            'last_reminder_sent_at' => $reminder->last_reminder_sent_at,
            'completed' => (bool) $reminder->completed_at,
            'can_send_again' => $reminder->canSendReminder(),
            'next_reminder_available_at' => $reminder->last_reminder_sent_at 
                ? $reminder->last_reminder_sent_at->addHours(48)
                : null,
        ];
    }

    public function getSystemStats(): array
    {
        $totalUsersWithoutCourses = $this->getUsersWithoutCourses()->count();
        $activeReminders = CourseCreationReminder::whereNull('completed_at')->count();
        $completedReminders = CourseCreationReminder::whereNotNull('completed_at')->count();

        return [
            'users_without_courses' => $totalUsersWithoutCourses,
            'active_reminders' => $activeReminders,
            'completed_reminders' => $completedReminders,
            'total_reminders_sent' => CourseCreationReminder::sum('reminder_count'),
        ];
    }
}