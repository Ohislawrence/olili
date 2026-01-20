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
    // In UserDirectEmail.php
    public $subject;
    public $message; // Changed from $bodyText
    public $fromEmail;
    public $fromName;

    public function __construct($subject, $message, $fromEmail = null, $fromName = null)
    {
        $this->subject = $subject;
        $this->message = $message; // Changed from $this->bodyText
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public function toMail($notifiable)
    {
        // Replace double brace variables
        $subject = str_replace(
            ['{{name}}', '{{email}}', '{{role}}', '{{app_name}}', '{{app_url}}', '{{year}}'],
            [
                $notifiable->name,
                $notifiable->email,
                $notifiable->roles->first()?->name ?? 'User',
                config('app.name'),
                config('app.url'),
                date('Y')
            ],
            $this->subject
        );

        $message = str_replace(
            ['{{name}}', '{{email}}', '{{role}}', '{{app_name}}', '{{app_url}}', '{{year}}'],
            [
                $notifiable->name,
                $notifiable->email,
                $notifiable->roles->first()?->name ?? 'User',
                config('app.name'),
                config('app.url'),
                date('Y')
            ],
            $this->message // Now using $this->message instead of $this->bodyText
        );

        $mail = (new MailMessage)
            ->subject($subject)
            ->markdown('emails.user-direct', [
                'subject' => $subject,
                'bodyText' => $message, // Pass as bodyText to the view
                'user' => $notifiable
            ]);

        if ($this->fromEmail) {
            $mail->from($this->fromEmail, $this->fromName);
        }

        return $mail;
    }

}
