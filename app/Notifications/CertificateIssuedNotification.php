<?php
// app/Notifications/CertificateIssuedNotification.php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CertificateIssuedNotification extends Notification
{
    use Queueable;

    public $certificate;

    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎓 Congratulations! Your Certificate is Ready')
            ->greeting("Congratulations {$this->certificate->user->name}!")
            ->line("You have successfully completed the course: **{$this->certificate->course->title}**")
            ->line('Your certificate is now available for download and sharing.')
            ->action('View Certificate', route('student.certificates.show', $this->certificate->id))
            ->line('Certificate Details:')
            ->line("- Certificate Number: {$this->certificate->certificate_number}")
            ->line("- Issue Date: {$this->certificate->issue_date->format('F j, Y')}")
            ->line("- Issued By: " . ($this->certificate->organization?->name ?? 'Olilearn'))
            ->salutation('Best regards,<br>The Olilearn Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'certificate_id' => $this->certificate->id,
            'certificate_number' => $this->certificate->certificate_number,
            'course_title' => $this->certificate->course->title,
            'message' => 'Your certificate has been issued!',
            'action_url' => route('student.certificates.show', $this->certificate->id),
        ];
    }
}