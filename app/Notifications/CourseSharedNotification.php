<?php
// app/Notifications/CourseSharedNotification.php

namespace App\Notifications;

use App\Models\Course;
use App\Models\CourseShare;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CourseSharedNotification extends Notification
{
    use Queueable;

    public $sharer;
    public $course;
    public $share;
    public $message;

    public function __construct(User $sharer, Course $course, CourseShare $share, $message = null)
    {
        $this->sharer = $sharer;
        $this->course = $course;
        $this->share = $share;
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("{$this->sharer->name} shared a course with you")
            ->greeting("Hello {$notifiable->name}!")
            ->line("{$this->sharer->name} has shared the course **{$this->course->title}** with you.")
            ->line($this->message ? "Message from {$this->sharer->name}: \"{$this->message}\"" : '')
            ->action('View & Accept Course', route('course.share.accept', $this->share->token))
            ->line('This invitation will expire in 7 days.')
            ->line('Thank you for using our platform!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'course_shared',
            'sharer_id' => $this->sharer->id,
            'sharer_name' => $this->sharer->name,
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'share_id' => $this->share->id,
            'message' => $this->message,
            'accept_url' => route('course.share.accept', $this->share->token),
            'reject_url' => route('course.share.reject', $this->share->token),
        ];
    }
}
