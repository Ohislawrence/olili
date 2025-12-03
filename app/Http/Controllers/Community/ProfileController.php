<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $user->loadCount(['communityPosts', 'followers', 'following']);
        
        $posts = $user->communityPosts()
            ->with(['course', 'comments'])
            ->latest()
            ->paginate(10);

        $isFollowing = auth()->check() && auth()->user()->isFollowing($user);

        return Inertia::render('Frontpages/Community/Profile', [
            'profileUser' => $user->load(['courses']),
            'posts' => $posts,
            'isFollowing' => $isFollowing,
            'stats' => $user->community_stats,
        ]);
    }

    public function follow(User $user)
    {
        auth()->user()->follow($user);
        
        return back()->with('success', 'Following ' . $user->name);
    }

    public function unfollow(User $user)
    {
        auth()->user()->unfollow($user);
        
        return back()->with('success', 'Unfollowed ' . $user->name);
    }

    public function followers(User $user)
    {
        $followers = $user->followers()
            ->with('follower')
            ->paginate(20);

        return Inertia::render('Frontpages/Community/Followers', [
            'profileUser' => $user,
            'followers' => $followers,
        ]);
    }

    public function following(User $user)
    {
        $following = $user->following()
            ->with('user')
            ->paginate(20);

        return Inertia::render('Frontpages/Community/Following', [
            'profileUser' => $user,
            'following' => $following,
        ]);
    }
}