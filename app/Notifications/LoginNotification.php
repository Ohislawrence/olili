<?php
// app/Notifications/LoginNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class LoginNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ipAddress;
    public $userAgent;
    public $location;
    public $time;

    /**
     * Create a new notification instance.
     */
    public function __construct($ipAddress = null, $userAgent = null, $location = null)
    {
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->location = $location;
        $this->time = Carbon::now();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Check if user wants email notifications (you can add this preference to users table)
        $channels = ['database', 'broadcast', 'mail'];

        if ($notifiable->email_notifications ?? true) { // Default to true if preference not set
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Login Detected - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We noticed a new login to your account:')
            ->line('**Time:** ' . $this->time->format('F j, Y \a\t g:i A'))
            ->line('**IP Address:** ' . ($this->ipAddress ?? 'Unknown'))
            ->line('**Location:** ' . ($this->location ?? 'Unknown'))
            ->line('**Device:** ' . $this->getDeviceInfo())
            ->action('View Account Security', url('/student/security'))
            ->line('If this was you, you can ignore this message. If you suspect unauthorized access, please secure your account immediately.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Login Detected',
            'message' => 'Your account was accessed from a new device or location.',
            'type' => 'security',
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'location' => $this->location,
            'device_info' => $this->getDeviceInfo(),
            'login_time' => $this->time->toISOString(),
            'action_url' => '/student/security',
            'action_text' => 'Review Security',
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Login Detected',
            'message' => 'Your account was accessed from ' . $this->getDeviceInfo() . ' in ' . ($this->location ?? 'an unknown location'),
            'type' => 'security',
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'location' => $this->location,
            'device_info' => $this->getDeviceInfo(),
            'login_time' => $this->time->toISOString(),
            'action_url' => '/student/security',
            'action_text' => 'Review Security',
        ];
    }

    /**
     * Extract device information from user agent
     */
    protected function getDeviceInfo(): string
    {
        if (!$this->userAgent) {
            return 'Unknown Device';
        }

        $deviceInfo = 'Unknown Device';

        // Simple device detection
        if (strpos($this->userAgent, 'Mobile') !== false) {
            $deviceInfo = 'Mobile Device';
        } elseif (strpos($this->userAgent, 'Tablet') !== false) {
            $deviceInfo = 'Tablet';
        } else {
            $deviceInfo = 'Desktop';
        }

        // Browser detection
        if (strpos($this->userAgent, 'Chrome') !== false) {
            $deviceInfo .= ' (Chrome)';
        } elseif (strpos($this->userAgent, 'Firefox') !== false) {
            $deviceInfo .= ' (Firefox)';
        } elseif (strpos($this->userAgent, 'Safari') !== false) {
            $deviceInfo .= ' (Safari)';
        } elseif (strpos($this->userAgent, 'Edge') !== false) {
            $deviceInfo .= ' (Edge)';
        }

        return $deviceInfo;
    }
}
