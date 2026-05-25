<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\User;
use App\Notifications\NewCoursePublished;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class NotifyStudentsOfNewCourse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $course;

    /**
     * Create a new job instance.
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all active students
        $students = User::role('student')->where('is_active', true)->get();

        // Send notifications in chunks to avoid memory issues if there are many students
        Notification::send($students, new NewCoursePublished($this->course));
    }
}
