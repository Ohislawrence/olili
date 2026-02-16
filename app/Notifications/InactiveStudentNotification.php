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
        $channels = [ 'database']; //i removed mail

        if (method_exists($notifiable, 'routeNotificationForWebPush') &&
            $notifiable->routeNotificationForWebPush()) {
            $channels[] = 'web-push';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("We Miss You! {$this->daysInactive} Days Inactive in {$this->course->title}")
            ->greeting("Hey {$notifiable->name},")
            ->line("We noticed you haven't made progress on **{$this->course->title}** for {$this->daysInactive} " .
                   ($this->daysInactive === 1 ? 'day' : 'days') . ".")
            ->line("Current Progress: **" . $this->getUserProgress($notifiable) . "%**")
            ->line("Remember why you started! Every small step brings you closer to your goal.")
            ->action('Jump Back In', route('courses.show', ['id' => $this->course->id, 'slug' => $this->course->slug]))
            ->line("Need help getting started? Try:")
            ->line("- Review the last topic you completed")
            ->line("- Take a quick quiz to refresh your memory")
            ->line("- Watch a short video lesson")
            ->salutation('You got this!<br>' . config('app.name'));
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'student_inactive',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'course_code' => $this->course->code,
            'days_inactive' => $this->daysInactive,
            'progress_percentage' => $this->getUserProgress($notifiable),
            'message' => "You've been inactive for {$this->daysInactive} days in '{$this->course->title}'",
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
}
