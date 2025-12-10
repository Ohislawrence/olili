<?php
// app/Notifications/CourseCreationReminder.php
namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseCreationReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $reminderCount;

    public function __construct($user, $reminderCount)
    {
        // Handle both User object and array
        if (is_array($user) && isset($user['id'])) {
            // If array passed, find the user
            $this->user = User::find($user['id']);
        } else if ($user instanceof User) {
            // If User object passed
            $this->user = $user;
        } else {
            throw new \InvalidArgumentException('Invalid user parameter');
        }

        $this->reminderCount = $reminderCount;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $subject = $this->getSubject();
        $message = $this->getMessage();

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.course-creation-reminder', [
                'user' => $this->user,
                'reminderCount' => $this->reminderCount,
                'message' => $message,
                'subject' => $subject,
            ])->text('emails.course-creation-reminder-plain', [
                'user' => $this->user,
                'reminderCount' => $this->reminderCount,
                'message' => $message,
                'subject' => $subject,
            ]);
    }

    protected function getSubject(): string
    {
        $subjects = [
            1 => 'Start Your Learning Journey on OliLearn! 🚀',
            2 => 'Don\'t Miss Out - Create Your First Course! 📚',
            3 => 'Final Reminder: Begin Your Educational Adventure! ⭐',
        ];

        return $subjects[$this->reminderCount + 1] ?? 'Continue Your Learning Journey on OliLearn';
    }

    protected function getMessage(): string
    {
        $messages = [
            1 => "We noticed you haven't created any courses yet. Your personalized learning experience is waiting for you! Create your first course and start your educational journey today.",
            2 => "You're just one step away from unlocking amazing learning opportunities. Create your first course and discover how OliLearn can transform your education.",
            3 => "This is your final reminder to start creating courses on OliLearn. Don't miss the chance to personalize your learning experience and achieve your educational goals.",
        ];

        return $messages[$this->reminderCount + 1] ?? "It's a great time to start creating courses on OliLearn and begin your learning journey!";
    }
}
