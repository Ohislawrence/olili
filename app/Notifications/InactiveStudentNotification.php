<?php
// app/Notifications/InactiveStudentNotification.php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InactiveStudentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Course $course, public int $daysInactive)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Reminder: Continue Your Course - {$this->course->title}")
            ->line("We noticed you haven't made progress on your course '{$this->course->title}' for {$this->daysInactive} days.")
            ->line("Don't forget to continue your learning journey!")
            ->action('Continue Learning', url("/courses/{$this->course->id}"))
            ->line('Stay motivated and keep learning!');
    }

    public function toArray($notifiable): array
    {
        return [
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'days_inactive' => $this->daysInactive,
            'message' => "You haven't progressed in '{$this->course->title}' for {$this->daysInactive} days.",
            'type' => 'inactive_student',
        ];
    }
}
