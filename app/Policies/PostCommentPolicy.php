<?php

namespace App\Policies;

use App\Models\PostComment;
use App\Models\User;

class PostCommentPolicy
{
    public function update(User $user, PostComment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isAdmin();
    }

    public function delete(User $user, PostComment $comment): bool
    {
        return $user->id === $comment->user_id || 
               $user->id === $comment->post->user_id || 
               $user->isAdmin();
    }
}