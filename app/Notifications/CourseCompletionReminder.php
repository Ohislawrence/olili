<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseCompletionReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $enrollment;
    protected $daysRemaining;
    protected $isOverdue;

    /**
     * Create a new notification instance.
     */
    public function __construct(CourseEnrollment $enrollment, int $daysRemaining, bool $isOverdue = false)
    {
        $this->enrollment = $enrollment;
        $this->daysRemaining = $daysRemaining;
        $this->isOverdue = $isOverdue;
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

        if ($this->isOverdue) {
            return (new MailMessage)
                ->subject("Your course completion is overdue by {$this->daysRemaining} days")
                ->greeting("Hello {$notifiable->name},")
                ->line("Your target completion date for **{$course->title}** has passed by **{$this->daysRemaining} days**.")
                ->line("Target completion date: {$this->enrollment->est_completion_time->format('M d, Y')}")
                ->line("Current progress: {$this->enrollment->progress_percentage}%")
                ->action('Complete Your Course', url("/courses/{$course->slug}/learn"))
                ->line("You're almost there! Let's finish strong!")
                ->salutation('Best regards,<br>Learning Platform Team');
        }

        return (new MailMessage)
            ->subject("Course completion reminder: {$this->daysRemaining} days remaining")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have **{$this->daysRemaining} days** left to complete **{$course->title}**.")
            ->line("Target completion date: {$this->enrollment->est_completion_time->format('M d, Y')}")
            ->line("Current progress: {$this->enrollment->progress_percentage}%")
            ->action('Continue Learning', url("/courses/{$course->slug}/learn"))
            ->line('Keep up the great work to meet your deadline!')
            ->salutation('Best regards,<br>Learning Platform Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = $this->isOverdue
            ? "Your course '{$this->enrollment->course->title}' is overdue by {$this->daysRemaining} days."
            : "You have {$this->daysRemaining} days remaining to complete '{$this->enrollment->course->title}'.";

        return [
            'type' => 'completion_reminder',
            'enrollment_id' => $this->enrollment->id,
            'course_id' => $this->enrollment->course_id,
            'course_title' => $this->enrollment->course->title,
            'days_remaining' => $this->daysRemaining,
            'is_overdue' => $this->isOverdue,
            'target_date' => $this->enrollment->est_completion_time,
            'current_progress' => $this->enrollment->progress_percentage,
            'message' => $message,
            'action_url' => "/courses/{$this->enrollment->course->slug}/learn",
        ];
    }
}
