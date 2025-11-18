<?php
// app/Policies/ChatSessionPolicy.php

namespace App\Policies;

use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatSessionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, ChatSession $chatSession)
    {
        return $chatSession->user_id === $user->id;
    }

    public function update(User $user, ChatSession $chatSession)
    {
        return $chatSession->user_id === $user->id;
    }

    public function delete(User $user, ChatSession $chatSession)
    {
        return $chatSession->user_id === $user->id;
    }
}
