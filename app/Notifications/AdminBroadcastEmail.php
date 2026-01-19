<?php
// app/Notifications/AdminBroadcastEmail.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminBroadcastEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public $subject;
    public $message;
    public $fromEmail;
    public $fromName;

    public function __construct($subject, $message, $fromEmail = null, $fromName = null)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Replace variables in subject and message
        $subject = str_replace(
            ['{{app_name}}', '{{app_url}}', '{{year}}'],
            [config('app.name'), config('app.url'), date('Y')],
            $this->subject
        );

        $message = str_replace(
            ['{{app_name}}', '{{app_url}}', '{{year}}'],
            [config('app.name'), config('app.url'), date('Y')],
            $this->message
        );

        $mail = (new MailMessage)
            ->subject($subject)
            ->markdown('emails.admin-broadcast', [
                'subject' => $subject,
                'message' => $message,
                'user' => $notifiable
            ]);

        if ($this->fromEmail) {
            $mail->from($this->fromEmail, $this->fromName);
        }

        return $mail;
    }
}
