<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseEnrollmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $course;
    public $enrollment;
    public $customMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course, CourseEnrollment $enrollment, $customMessage = null)
    {
        $this->course = $course;
        $this->enrollment = $enrollment;
        $this->customMessage = $customMessage;
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
        return (new MailMessage)
            ->subject('🎓 You have been enrolled in a new course!')
            ->greeting("Hello {$notifiable->name},")
            ->line($this->customMessage ?? "You have been enrolled in the course: **{$this->course->title}**")
            ->line('**Course Details:**')
            ->line("• Subject: {$this->course->subject}")
            ->line("• Level: " . ucfirst($this->course->level))
            ->line("• Estimated Duration: {$this->course->estimated_duration_hours} hours")
            ->line("• Start Date: " . $this->course->start_date?->format('F j, Y') ?? 'Immediately')
            ->action('Start Learning', route('student.courses.learn', $this->course->id))
            ->line('We\'re excited to have you on this learning journey!')
            ->salutation('Best Regards,<br>The Learning Platform Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'course_enrollment',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'enrollment_id' => $this->enrollment->id,
            'message' => $this->customMessage ?? "You have been enrolled in '{$this->course->title}'",
            'action_url' => route('student.courses.learn', $this->course->id),
            'icon' => '🎓',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'emails',
            'database' => 'database',
        ];
    }
}
