<?php
// app/Jobs/SendCourseInvitationEmail.php

namespace App\Jobs;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseShare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\CourseSharedInvitationMail;

class SendCourseInvitationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    public $sharer;
    public $course;
    public $share;
    public $message;

    public function __construct($email, User $sharer, Course $course, CourseShare $share, $message = null)
    {
        $this->email = $email;
        $this->sharer = $sharer;
        $this->course = $course;
        $this->share = $share;
        $this->message = $message;
    }

    public function handle()
    {
        Mail::to($this->email)
            ->send(new CourseSharedInvitationMail(
                $this->sharer,
                $this->course,
                $this->share,
                $this->message
            ));
    }
}
