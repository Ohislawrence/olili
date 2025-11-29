<?php
// app/Notifications/UserDirectEmail.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserDirectEmail extends Notification implements ShouldQueue
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
        $mail = (new MailMessage)
            ->subject($this->subject)
            ->markdown('emails.user-direct', [
                'message' => $this->message,
                'user' => $notifiable
            ]);

        if ($this->fromEmail) {
            $mail->from($this->fromEmail, $this->fromName);
        }

        return $mail;
    }
}