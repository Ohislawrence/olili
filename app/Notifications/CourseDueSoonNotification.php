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
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Course Due Soon: {$this->course->title}")
            ->line("Your course '{$this->course->title}' is due in {$this->daysRemaining} days.")
            ->line("Please continue working on it to complete before the deadline.")
            ->action('Continue Course', url("/courses/{$this->course->id}"))
            ->line('Thank you for using our platform!');
    }

    public function toArray($notifiable): array
    {
        return [
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'due_date' => $this->course->target_completion_date,
            'days_remaining' => $this->daysRemaining,
            'message' => "Course '{$this->course->title}' is due in {$this->daysRemaining} days.",
            'type' => 'course_due_soon',
        ];
    }
}
