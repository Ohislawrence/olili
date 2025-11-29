<?php
// app/Mail/WelcomeStudentMail.php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeStudentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $learningRecommendations;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Welcome to Olilearn - Start Your Learning Journey!')
                ->markdown('emails.welcome-student', [
                    'user' => $this->user,
                ])
                ->text('emails.welcome-student-plain');
    }

    protected function getLearningRecommendations(User $user)
    {
        $profile = $user->studentProfile;
        $recommendations = [];

        if ($profile->current_level === 'beginner') {
            $recommendations[] = 'Start with our "Learning Fundamentals" course';
        }

        if (in_array('exam_preparation', $profile->learning_goals ?? [])) {
            $recommendations[] = 'Check out our exam preparation resources';
        }

        if (in_array('visual', $profile->learning_preferences ?? [])) {
            $recommendations[] = 'Explore our visual learning materials';
        }

        return $recommendations;
    }
}
