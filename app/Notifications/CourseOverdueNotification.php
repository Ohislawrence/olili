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
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Course Overdue: {$this->course->title}")
            ->line("Your course '{$this->course->title}' was due {$this->daysOverdue} days ago.")
            ->line("Please complete it as soon as possible.")
            ->action('Complete Course', url("/courses/{$this->course->id}"))
            ->line('Thank you for using our platform!');
    }

    public function toArray($notifiable): array
    {
        return [
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'due_date' => $this->course->target_completion_date,
            'days_overdue' => $this->daysOverdue,
            'message' => "Course '{$this->course->title}' is {$this->daysOverdue} days overdue.",
            'type' => 'course_overdue',
        ];
    }
}
