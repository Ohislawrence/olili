<?php

namespace App\Policies;

use App\Models\CommunityPost;
use App\Models\User;

class CommunityPostPolicy
{
    public function update(User $user, CommunityPost $post): bool
    {
        return $user->id === $post->user_id || $user->isAdmin();
    }

    public function delete(User $user, CommunityPost $post): bool
    {
        return $user->id === $post->user_id || $user->isAdmin();
    }
}
