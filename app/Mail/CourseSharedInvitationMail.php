<?php
// app/Mail/CourseSharedInvitationMail.php

namespace App\Mail;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseShare;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourseSharedInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sharer;
    public $course;
    public $share;
    public $message;

    public function __construct(User $sharer, Course $course, CourseShare $share, $message = null)
    {
        $this->sharer = $sharer;
        $this->course = $course;
        $this->share = $share;
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject("{$this->sharer->name} shared a course with you")
            ->markdown('emails.course-shared-invitation')
            ->with([
                'sharer' => $this->sharer,
                'course' => $this->course,
                'share' => $this->share,
                'message' => $this->message,
            ]);
    }
}
