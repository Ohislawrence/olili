<?php
// app/Console/Commands/SendCourseCreationReminders.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CourseCreationReminderService;

class SendCourseCreationReminders extends Command
{
    protected $signature = 'reminders:send-course-creation';
    protected $description = 'Send course creation reminders to users without courses';

    public function handle(CourseCreationReminderService $reminderService)
    {
        $this->info('Starting course creation reminders...');
        
        $sentCount = $reminderService->sendReminders();
        
        $stats = $reminderService->getSystemStats();
        
        $this->info("Successfully sent {$sentCount} reminders.");
        $this->info("System stats:");
        $this->info("- Users without courses: {$stats['users_without_courses']}");
        $this->info("- Active reminders: {$stats['active_reminders']}");
        $this->info("- Completed reminders: {$stats['completed_reminders']}");
        $this->info("- Total reminders sent: {$stats['total_reminders_sent']}");
        
        return Command::SUCCESS;
    }
}