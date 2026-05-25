<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCoursePublished extends Notification implements ShouldQueue
{
    use Queueable;

    public $course;

    /**
     * Create a new notification instance.
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
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
            ->subject('🚀 New Course Available: ' . $this->course->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We are excited to announce that a new course has just been added to Olilearn.')
            ->line('**Course Name:** ' . $this->course->title)
            ->line('**Subject:** ' . $this->course->subject)
            ->line('**Level:** ' . ucfirst($this->course->level))
            ->line('Start learning today and advance your skills!')
            ->action('View Course Details', route('student.catalog.show', $this->course->slug))
            ->line('Happy learning!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'subject' => $this->course->subject,
            'message' => 'New course available: ' . $this->course->title,
            'url' => route('student.catalog.show', $this->course->slug),
            'type' => 'new_course_published'
        ];
    }
}
