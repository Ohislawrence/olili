<?php
// app/Console/Commands/SendWeeklyProgressEmails.php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Course;
use App\Mail\WeeklyProgressEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SendWeeklyProgressEmails extends Command
{
    protected $signature = 'emails:send-weekly-progress';

    protected $description = 'Send weekly progress emails to users every Monday morning';

    public function handle()
    {
        $this->info('Starting weekly progress email dispatch...');

        $weekNumber = now()->weekOfYear;
        $sentCount = 0;

        // Get all active users (excluding admins)
        User::where('is_active', true)
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['student', 'tutor']);
            })
            ->chunk(100, function ($users) use ($weekNumber, &$sentCount) {
                foreach ($users as $user) {
                    $this->sendEmailToUser($user, $weekNumber);
                    $sentCount++;
                }
            });

        $this->info("Weekly progress emails dispatched successfully. Sent to {$sentCount} users.");

        return Command::SUCCESS;
    }

    private function sendEmailToUser(User $user)
    {
        try {
            // Get up to 3 enrolled courses that aren't completed
            $enrolledCourses = $this->getUserEnrolledCourses($user);

            // Get 5 random suggested courses
            $suggestedCourses = $this->getSuggestedCourses($user);
            $weekNumber =now()->weekOfYear;

            // Check if user has any enrolled courses to determine if we should send
            if ($enrolledCourses->count() > 0 || $suggestedCourses->count() > 0) {
                Mail::to($user->email)->queue(new WeeklyProgressEmail(
                    $user,
                    $enrolledCourses,
                    $suggestedCourses,
                    $weekNumber
                ));

                $this->info("Email sent to: {$user->email}");
            }
        } catch (\Exception $e) {
            $this->error("Failed to send email to {$user->email}: " . $e->getMessage());
            \Log::error("Weekly email error for user {$user->id}: " . $e->getMessage());
        }
    }

    private function getUserEnrolledCourses(User $user): \Illuminate\Support\Collection
    {
        // Explicitly specify which table's status column to use
        return $user->enrolledCourses()
            ->wherePivot('status', '!=', 'completed')  // Use wherePivot for pivot table columns
            ->wherePivot('status', '!=', 'dropped')
            ->orderByPivot('progress_percentage', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'progress' => $course->pivot->progress_percentage ?? 0,
                    'status' => $course->pivot->status,
                    'enrolled_at' => $course->pivot->enrolled_at,
                    'completed_at' => $course->pivot->completed_at,
                    'course_url' => route('courses.show', ['id' => $course->id, 'slug' => $course->slug]),
                ];
            });
    }

    private function getSuggestedCourses(User $user): \Illuminate\Support\Collection
    {
        // Get courses user is not enrolled in
        $enrolledCourseIds = $user->enrolledCourses->pluck('id')->toArray();

        // Get 5 random suggested courses based on:
        // 1. User's subject preferences (if available)
        // 2. Popular courses
        // 3. New courses
        // 4. Courses similar to enrolled courses

        $suggestedCourses = Course::where('status', 'active')
            ->where('is_public', true)
            ->where('visibility', 'public')
            ->whereNotIn('id', $enrolledCourseIds)
            ->with(['subject', 'examBoard'])
            ->inRandomOrder()
            ->limit(5)
            ->get()
            ->map(function ($course) {
                return [
                    'title' => $course->title,
                    'subject' => $course->subject->name ?? 'General',
                    'level' => $course->level,
                    'description' => Str::limit($course->description, 100),
                    'enrolled_count' => $course->current_enrollment,
                    'estimated_duration' => $course->estimated_duration_hours,
                    'course_url' => route('courses.show', ['id' => $course->id, 'slug' => $course->slug]),
                    'enroll_url' => route('courses.show', ['id' => $course->id, 'slug' => $course->slug]),
                ];
            });

        return $suggestedCourses;
    }
}
