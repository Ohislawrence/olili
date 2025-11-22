<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data = [])
    {
        $this->data = array_merge([
            'greeting' => 'Hello!',
            'introLines' => [],
            'actionText' => null,
            'actionUrl' => null,
            'outroLines' => [],
            'salutation' => 'Regards,<br>' . config('app.name'),
            'subject' => 'Notification from ' . config('app.name'),
        ], $data);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->data['subject'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: $this->data,
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
