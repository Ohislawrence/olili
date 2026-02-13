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
        // Extract first name for more personal greeting
        $firstName = explode(' ', trim($notifiable->name))[0];

        // Get role name safely
        $roleName = 'User';
        if ($notifiable->roles && $notifiable->roles->isNotEmpty()) {
            $roleName = $notifiable->roles->first()->name ?? 'User';
        }

        // Replace variables in subject
        $subject = $this->replaceVariables(
            $this->subject,
            $notifiable,
            $firstName,
            $roleName
        );

        // Replace variables in message
        $message = $this->replaceVariables(
            $this->message,
            $notifiable,
            $firstName,
            $roleName
        );

        $mail = (new MailMessage)
            ->subject($subject)
            ->markdown('emails.user-direct', [
                'subject' => $subject,
                'bodyText' => $message,
                'user' => $notifiable,
                'firstName' => $firstName, // Pass first name to view
            ]);

        // Set custom from address if provided
        if ($this->fromEmail) {
            $mail->from($this->fromEmail, $this->fromName ?? config('app.name'));
        }

        return $mail;
    }

    /**
     * Replace variables in text with user data
     */
    private function replaceVariables($text, $user, $firstName, $roleName)
    {
        if (empty($text)) {
            return $text;
        }

        $replacements = [
            '{{name}}' => $firstName,
            '{{full_name}}' => $user->name,
            '{{first_name}}' => $firstName,
            '{{last_name}}' => $this->getLastName($user->name),
            '{{email}}' => $user->email,
            '{{role}}' => $roleName,
            '{{app_name}}' => config('app.name'),
            '{{app_url}}' => config('app.url'),
            '{{year}}' => date('Y'),
            '{{date}}' => now()->format('F j, Y'),
            '{{time}}' => now()->format('g:i A'),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * Extract last name from full name
     */
    private function getLastName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        return count($parts) > 1 ? end($parts) : $fullName;
    }

    /**
     * Handle failures gracefully
     */
    public function failed(\Exception $e)
    {
        \Log::error('UserDirectEmail failed: ' . $e->getMessage());
    }
}
