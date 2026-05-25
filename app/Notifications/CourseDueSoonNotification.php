<?php
// app/Notifications/CourseDueSoonNotification.php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseDueSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Course $course, public int $daysRemaining)
    {
    }

    public function via($notifiable): array
    {
        $channels = ['mail', 'database'];

        if (method_exists($notifiable, 'routeNotificationForWebPush') &&
            $notifiable->routeNotificationForWebPush()) {
            $channels[] = 'web-push';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $urgency = $this->daysRemaining <= 3 ? 'urgent' : 'reminder';
        $emoji = $this->daysRemaining <= 3 ? '⏰' : '📅';

        return (new MailMessage)
            ->subject("{$emoji} Course Due in {$this->daysRemaining} Days: {$this->course->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your course **{$this->course->title}** is due in **{$this->daysRemaining} " .
                   ($this->daysRemaining === 1 ? 'day' : 'days') . "**.")
            ->line("Current Progress: **" . $this->getUserProgress($notifiable) . "%**")
            ->line($this->getMotivationalMessage())
            ->action('Continue Course', route('courses.show', ['id' => $this->course->id, 'slug' => $this->course->slug]))
            ->line("You can do it! Just a little more to go.")
            ->salutation('Best regards,<br>' . config('app.name'));
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'course_due_soon',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'course_code' => $this->course->code,
            'due_date' => $this->course->target_completion_date?->toDateTimeString(),
            'days_remaining' => $this->daysRemaining,
            'progress_percentage' => $this->getUserProgress($notifiable),
            'message' => $this->getNotificationMessage(),
            'action_url' => route('courses.show', ['id' => $this->course->id, 'slug' => $this->course->slug]),
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    private function getUserProgress($notifiable): float
    {
        $enrollment = $notifiable->enrollments()
            ->where('course_id', $this->course->id)
            ->first();

        return $enrollment ? (float) $enrollment->progress_percentage : 0.0;
    }

    private function getMotivationalMessage(): string
    {
        if ($this->daysRemaining <= 1) {
            return "Final stretch! Complete your course today to stay on track.";
        } elseif ($this->daysRemaining <= 3) {
            return "Time to push through! You're so close to finishing.";
        } else {
            return "Keep up the momentum! Plan your study sessions for this week.";
        }
    }

    private function getNotificationMessage(): string
    {
        $daysText = $this->daysRemaining === 1 ? '1 day' : "{$this->daysRemaining} days";
        return "Course '{$this->course->title}' is due in {$daysText}. Complete it on time!";
    }
}
