<?php
// app/Policies/QuizAttemptPolicy.php

namespace App\Policies;

use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuizAttemptPolicy
{
    use HandlesAuthorization;

    public function view(User $user, QuizAttempt $quizAttempt)
    {
        return $quizAttempt->user_id === $user->id;
    }

    public function update(User $user, QuizAttempt $quizAttempt)
    {
        return $quizAttempt->user_id === $user->id;
    }
}
