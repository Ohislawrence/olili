<?php
// app/Notifications/CourseQuizzesGenerated.php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CourseQuizzesGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The course for which quizzes were generated
     */
    public Course $course;

    /**
     * Number of quizzes successfully generated
     */
    public int $generatedCount;

    /**
     * Number of quizzes that failed to generate
     */
    public int $failedCount;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course, int $generatedCount, int $failedCount)
    {
        $this->course = $course;
        $this->generatedCount = $generatedCount;
        $this->failedCount = $failedCount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $total = $this->generatedCount + $this->failedCount;

        return (new MailMessage)
            ->subject(sprintf('Quiz Generation Complete: %s', $this->course->title))
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line(sprintf(
                'The quiz generation process for **%s** has been completed.',
                $this->course->title
            ))
            ->line('')
            ->line('**Generation Summary:**')
            ->line(sprintf('✅ **Successfully Generated:** %d quizzes', $this->generatedCount))
            ->line(sprintf('❌ **Failed to Generate:** %d quizzes', $this->failedCount))
            ->line(sprintf('📊 **Total Attempted:** %d quizzes', $total))
            ->line('')
            ->action('View Course Details', route('admin.courses.show', $this->course->id))
            ->line('You can review the generated quizzes and take necessary actions if any failed.')
            ->line('')
            ->lineIf($this->failedCount > 0, sprintf(
                '**Note:** %d quizzes failed to generate. You may need to regenerate them manually.',
                $this->failedCount
            ))
            ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation for database storage.
     */
    public function toDatabase(object $notifiable): array
    {
        $total = $this->generatedCount + $this->failedCount;

        return [
            'type' => 'quiz_generation',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'course_code' => $this->course->code,
            'generated_count' => $this->generatedCount,
            'failed_count' => $this->failedCount,
            'total_attempted' => $total,
            'success_rate' => $total > 0 ? round(($this->generatedCount / $total) * 100, 2) : 0,
            'message' => sprintf(
                'Quiz generation completed for %s: %d successful, %d failed',
                $this->course->title,
                $this->generatedCount,
                $this->failedCount
            ),
            'action_url' => route('admin.courses.show', $this->course->id),
            'action_text' => 'View Course',
            'icon' => $this->failedCount > 0 ? 'exclamation-circle' : 'check-circle',
            'icon_color' => $this->failedCount > 0 ? 'warning' : 'success',
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $total = $this->generatedCount + $this->failedCount;

        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'quiz_generation',
            'title' => 'Quiz Generation Complete',
            'message' => sprintf(
                'Quiz generation completed for %s: %d successful, %d failed',
                $this->course->title,
                $this->generatedCount,
                $this->failedCount
            ),
            'data' => [
                'course_id' => $this->course->id,
                'course_title' => $this->course->title,
                'generated_count' => $this->generatedCount,
                'failed_count' => $this->failedCount,
                'total_attempted' => $total,
                'success_rate' => $total > 0 ? round(($this->generatedCount / $total) * 100, 2) : 0,
                'has_failures' => $this->failedCount > 0,
            ],
            'action_url' => route('admin.courses.show', $this->course->id),
            'action_text' => 'View Course',
            'icon' => $this->failedCount > 0 ? 'exclamation-circle' : 'check-circle',
            'icon_color' => $this->failedCount > 0 ? 'warning' : 'success',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get the notification's unique identifier.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'notifications',
            'database' => 'notifications',
            'broadcast' => 'notifications',
        ];
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function withDelay(object $notifiable): array
    {
        return [
            'mail' => now()->addSeconds(10),
            'database' => null,
            'broadcast' => null,
        ];
    }
}
