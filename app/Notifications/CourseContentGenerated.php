<?php
// app/Notifications/CourseContentGenerated.php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class CourseContentGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    public $course;
    public $generatedCount;
    public $failedCount;

    public function __construct(Course $course, int $generatedCount, int $failedCount)
    {
        $this->course = $course;
        $this->generatedCount = $generatedCount;
        $this->failedCount = $failedCount;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('✅ Course Content Generation Complete: ' . $this->course->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The course content generation has been completed successfully!')
            ->line('**Course:** ' . $this->course->title)
            ->line('**Content Generated:** ' . $this->generatedCount . ' topics')
            ->line('**Failed:** ' . $this->failedCount . ' topics')
            ->action('View Course', route('admin.courses.show', $this->course->id))
            ->line('You can now review the generated content and publish the course when ready.')
            ->line('Thank you for using our platform!');
    }

    public function toArray($notifiable)
    {
        return [
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'generated_count' => $this->generatedCount,
            'failed_count' => $this->failedCount,
            'message' => 'Content generation completed for ' . $this->course->title,
            'link' => route('admin.courses.show', $this->course->id),
            'type' => 'content_generation_complete',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'data' => $this->toArray($notifiable),
            'created_at' => now(),
            'read_at' => null,
        ]);
    }
}
