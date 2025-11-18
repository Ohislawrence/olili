<?php
// app/Policies/CapstoneProjectPolicy.php

namespace App\Policies;

use App\Models\CapstoneProject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CapstoneProjectPolicy
{
    use HandlesAuthorization;

    public function view(User $user, CapstoneProject $capstoneProject)
    {
        return $capstoneProject->course->student_profile_id === $user->studentProfile->id;
    }

    public function update(User $user, CapstoneProject $capstoneProject)
    {
        return $capstoneProject->course->student_profile_id === $user->studentProfile->id;
    }
}
