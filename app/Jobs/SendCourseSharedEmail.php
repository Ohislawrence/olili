<?php
// app/Jobs/SendCourseSharedEmail.php

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
use App\Mail\CourseSharedMail;

class SendCourseSharedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recipient;
    public $sharer;
    public $course;
    public $share;
    public $message;

    public function __construct(User $recipient, User $sharer, Course $course, CourseShare $share, $message = null)
    {
        $this->recipient = $recipient;
        $this->sharer = $sharer;
        $this->course = $course;
        $this->share = $share;
        $this->message = $message;
    }

    public function handle()
    {
        Mail::to($this->recipient->email)
            ->send(new CourseSharedMail(
                $this->sharer,
                $this->course,
                $this->share,
                $this->message
            ));
    }
}
