<?php
// app/Mail/CourseSharedMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourseSharedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sharer;
    public $course;
    public $share;
    public $message;

    public function __construct($sharer, $course, $share, $message = null)
    {
        $this->sharer = $sharer;
        $this->course = $course;
        $this->share = $share;
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject("{$this->sharer->name} shared a course with you")
            ->markdown('emails.course-shared')
            ->with([
                'sharer' => $this->sharer,
                'course' => $this->course,
                'share' => $this->share,
                'message' => $this->message,
            ]);
    }
}
