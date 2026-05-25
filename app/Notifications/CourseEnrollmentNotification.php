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

    public $courseId;
    public $enrollmentId;
    public $customMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct($courseId, $enrollmentId, $customMessage = null)
    {
        $this->courseId = $courseId;
        $this->enrollmentId = $enrollmentId;
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
        // Load the models inside the method
        $course = Course::find($this->courseId);
        $enrollment = CourseEnrollment::find($this->enrollmentId);

        // Handle missing course or enrollment
        if (!$course) {
            \Log::error("Course not found for notification: {$this->courseId}");
            return (new MailMessage)
                ->subject('Course Enrollment Confirmation')
                ->greeting("Hello {$notifiable->name},")
                ->line('You have been enrolled in a new course!')
                ->action('View Your Courses', route('student.courses.index'))
                ->line('We\'re excited to have you on this learning journey!')
                ->salutation('Best Regards,<br>The Learning Platform Team');
        }

        if (!$enrollment) {
            \Log::error("Enrollment not found for notification: {$this->enrollmentId}");
        }

        return (new MailMessage)
            ->subject('You have been enrolled in a new course!')
            ->greeting("Hello {$notifiable->name},")
            ->line($this->customMessage ?? "You have been enrolled in the course: **{$course->title}**")
            ->line('**Course Details:**')
            ->line("• Subject: {$course->subject}")
            ->line("• Level: " . ucfirst($course->level))
            ->line("• Estimated Duration: {$course->estimated_duration_hours} hours")
            ->line("• Start Date: " . ($course->start_date?->format('F j, Y') ?? 'Immediately'))
            ->action('Start Learning', route('student.courses.learn', $course->id))
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
        // Load the course (only need course for array representation)
        $course = Course::find($this->courseId);

        if (!$course) {
            return [
                'type' => 'course_enrollment',
                'course_id' => $this->courseId,
                'course_title' => 'Course (Details Unavailable)',
                'enrollment_id' => $this->enrollmentId,
                'message' => $this->customMessage ?? 'You have been enrolled in a new course',
                'action_url' => route('student.courses.index'),
                'icon' => '🎓',
                'timestamp' => now()->toISOString(),
            ];
        }

        return [
            'type' => 'course_enrollment',
            'course_id' => $this->courseId,
            'course_title' => $course->title,
            'enrollment_id' => $this->enrollmentId,
            'message' => $this->customMessage ?? "You have been enrolled in '{$course->title}'",
            'action_url' => route('student.courses.learn', $this->courseId),
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
