<?php
// app/Notifications/CourseQuizGenerationFailed.php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CourseQuizGenerationFailed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The course for which quiz generation failed
     */
    public Course $course;

    /**
     * Error message from the exception
     */
    public string $errorMessage;

    /**
     * Stack trace for debugging (optional)
     */
    public ?string $stackTrace;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course, string $errorMessage, ?string $stackTrace = null)
    {
        $this->course = $course;
        $this->errorMessage = $errorMessage;
        $this->stackTrace = $stackTrace;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Send via email and database only for failed notifications
        // Broadcasting might be too noisy for failures
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject(sprintf('❌ Quiz Generation Failed: %s', $this->course->title))
            ->greeting('Alert: Quiz Generation Issue')
            ->error() // Adds red styling to indicate error
            ->line(sprintf(
                'Quiz generation for course **%s** has failed.',
                $this->course->title
            ))
            ->line('')
            ->line('**Error Details:**')
            ->line('```')
            ->line($this->errorMessage)
            ->line('```')
            ->line('')
            ->line('**Course Information:**')
            ->line(sprintf('- Course: %s (%s)', $this->course->title, $this->course->code))
            ->line(sprintf('- Subject: %s', $this->course->subject))
            ->line(sprintf('- Level: %s', $this->course->level))
            ->line('')
            ->action('View Course', route('admin.courses.show', $this->course->id))
            ->action('Retry Generation', route('admin.courses.regenerate-quizzes', $this->course->id))
            ->line('');

        // Add stack trace if available (in collapsed section)
        if ($this->stackTrace) {
            $mail->line('<details>')
                 ->line('<summary><strong>Click to view technical details</strong></summary>')
                 ->line('')
                 ->line('**Stack Trace:**')
                 ->line('```')
                 ->line(substr($this->stackTrace, 0, 1000) . '...') // Limit stack trace length
                 ->line('```')
                 ->line('</details>');
        }

        return $mail->line('Please investigate and take necessary action.')
                   ->line('Thank you for your attention to this matter.');
    }

    /**
     * Get the array representation for database storage.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'quiz_generation_failed',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'course_code' => $this->course->code,
            'course_subject' => $this->course->subject,
            'course_level' => $this->course->level,
            'error_message' => $this->errorMessage,
            'error_preview' => substr($this->errorMessage, 0, 200) . (strlen($this->errorMessage) > 200 ? '...' : ''),
            'has_stack_trace' => !empty($this->stackTrace),
            'message' => sprintf(
                'Quiz generation failed for %s: %s',
                $this->course->title,
                substr($this->errorMessage, 0, 100)
            ),
            'action_url' => route('admin.courses.show', $this->course->id),
            'action_text' => 'View Course',
            'retry_url' => route('admin.courses.regenerate-quizzes', $this->course->id),
            'retry_text' => 'Retry Generation',
            'icon' => 'exclamation-triangle',
            'icon_color' => 'danger',
            'priority' => 'high',
            'requires_action' => true,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     * Only broadcast for critical failures or if requested
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'quiz_generation_failed',
            'title' => 'Quiz Generation Failed',
            'message' => sprintf(
                'Quiz generation failed for %s',
                $this->course->title
            ),
            'data' => [
                'course_id' => $this->course->id,
                'course_title' => $this->course->title,
                'course_code' => $this->course->code,
                'error_preview' => substr($this->errorMessage, 0, 100),
                'is_critical' => true,
                'requires_action' => true,
            ],
            'action_url' => route('admin.courses.show', $this->course->id),
            'action_text' => 'Investigate',
            'icon' => 'exclamation-triangle',
            'icon_color' => 'danger',
            'priority' => 'high',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'notifications-high',
            'database' => 'notifications',
            'broadcast' => 'notifications',
        ];
    }

    /**
     * Set delay for notification delivery.
     * Immediate delivery for failure notifications.
     */
    public function withDelay(object $notifiable): array
    {
        return [
            'mail' => null, // Send immediately
            'database' => null,
            'broadcast' => null,
        ];
    }

    /**
     * Get the tags for the notification.
     */
    public function tags(): array
    {
        return [
            'quiz-generation',
            'failed',
            'course:' . $this->course->id,
            'course:' . $this->course->code,
        ];
    }
}
