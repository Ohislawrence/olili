<?php
// app/Notifications/CourseOverdueNotification.php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Course $course, public int $daysOverdue)
    {
    }

    public function via($notifiable): array
    {
        // Check if user has push subscriptions enabled
        $channels = ['mail', 'database'];

        if (method_exists($notifiable, 'routeNotificationForWebPush') &&
            $notifiable->routeNotificationForWebPush()) {
            $channels[] = 'web-push'; // Add web push if available
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $subject = $this->daysOverdue === 1
            ? "⏰ Course Due Yesterday: {$this->course->title}"
            : "⏰ Course Overdue by {$this->daysOverdue} Days: {$this->course->title}";

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line("Your course **{$this->course->title}** was due {$this->daysOverdue} " .
                   ($this->daysOverdue === 1 ? 'day' : 'days') . " ago.")
            ->line("Current Progress: **" . $this->getUserProgress($notifiable) . "%**")
            ->line($this->getMotivationalMessage())
            ->action('Continue Course', route('courses.show', ['id' => $this->course->id, 'slug' => $this->course->slug]))
            ->line('Need help? Reach out to our support team.')
            ->salutation('Best regards,<br>' . config('app.name'));
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'course_overdue',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'course_code' => $this->course->code,
            'due_date' => $this->course->target_completion_date?->toDateTimeString(),
            'days_overdue' => $this->daysOverdue,
            'progress_percentage' => $this->getUserProgress($notifiable),
            'message' => $this->getNotificationMessage(),
            'action_url' => route('courses.show', ['id' => $this->course->id, 'slug' => $this->course->slug]),
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get web push notification data
     */
    public function toWebPush($notifiable, $notification)
    {
        return [
            'title' => "Course Overdue: {$this->course->title}",
            'body' => $this->getNotificationMessage(),
            'icon' => asset('images/notification-icon.png'),
            'badge' => asset('images/notification-badge.png'),
            'vibrate' => [200, 100, 200],
            'tag' => 'course-overdue-' . $this->course->id,
            'renotify' => true,
            'requireInteraction' => true,
            'data' => [
                'url' => route('courses.show', ['id' => $this->course->id, 'slug' => $this->course->slug]),
                'course_id' => $this->course->id,
            ],
            'actions' => [
                [
                    'action' => 'open-course',
                    'title' => 'Open Course',
                    'icon' => asset('images/open-icon.png'),
                ],
                [
                    'action' => 'dismiss',
                    'title' => 'Dismiss',
                    'icon' => asset('images/dismiss-icon.png'),
                ],
            ],
        ];
    }

    /**
     * Get user's progress percentage for this course
     */
    private function getUserProgress($notifiable): float
    {
        $enrollment = $notifiable->enrollments()
            ->where('course_id', $this->course->id)
            ->first();

        return $enrollment ? (float) $enrollment->progress_percentage : 0.0;
    }

    /**
     * Get motivational message based on days overdue
     */
    private function getMotivationalMessage(): string
    {
        if ($this->daysOverdue <= 3) {
            return "You're almost there! Just a little more effort to complete this course.";
        } elseif ($this->daysOverdue <= 7) {
            return "It's never too late to finish! You've already made great progress.";
        } else {
            return "Let's get you back on track. Complete this course to unlock your certificate!";
        }
    }

    /**
     * Get notification message for database/push
     */
    private function getNotificationMessage(): string
    {
        $daysText = $this->daysOverdue === 1 ? '1 day' : "{$this->daysOverdue} days";

        return "Course '{$this->course->title}' is {$daysText} overdue. Complete it now!";
    }
}
