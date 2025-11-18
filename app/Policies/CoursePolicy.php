<?php
// app/Policies/CoursePolicy.php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Course $course)
    {
        return $course->student_profile_id === $user->studentProfile->id;
    }

    public function update(User $user, Course $course)
    {
        return $course->student_profile_id === $user->studentProfile->id;
    }

    public function delete(User $user, Course $course)
    {
        return $course->student_profile_id === $user->studentProfile->id;
    }
}
