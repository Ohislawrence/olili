<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InactivityReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $enrollment;
    protected $inactiveDays;
    protected $lastActivityDate;

    /**
     * Create a new notification instance.
     */
    public function __construct(CourseEnrollment $enrollment, int $inactiveDays, $lastActivityDate = null)
    {
        $this->enrollment = $enrollment;
        $this->inactiveDays = $inactiveDays;
        $this->lastActivityDate = $lastActivityDate;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $course = $this->enrollment->course;

        return (new MailMessage)
            ->subject("You haven't studied for {$this->inactiveDays} days")
            ->greeting("Hello {$notifiable->name},")
            ->line("We noticed you haven't studied in **{$course->title}** for **{$this->inactiveDays} days**.")
            ->line("Your last activity was on: " . ($this->lastActivityDate ? $this->lastActivityDate->format('M d, Y') : 'Not recorded'))
            ->action('Continue Learning', url("/courses/{$course->slug}/learn"))
            ->line('Stay consistent to achieve your learning goals!')
            ->salutation('Best regards,<br>Learning Platform Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'inactivity_reminder',
            'enrollment_id' => $this->enrollment->id,
            'course_id' => $this->enrollment->course_id,
            'course_title' => $this->enrollment->course->title,
            'inactive_days' => $this->inactiveDays,
            'last_activity_date' => $this->lastActivityDate,
            'message' => "You haven't studied in '{$this->enrollment->course->title}' for {$this->inactiveDays} days.",
            'action_url' => "/courses/{$this->enrollment->course->slug}/learn",
        ];
    }
}
