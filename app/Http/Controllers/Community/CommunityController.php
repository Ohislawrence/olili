<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'all');
        $sort = $request->input('sort', 'recent');
        $search = $request->input('search');

        $posts = CommunityPost::with(['user', 'course'])
            ->where('is_approved', true) // Only show approved posts
            ->when($type && $type !== 'all', function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($sort === 'popular', function ($query) {
                return $query->popular();
            })
            ->when($sort === 'recent', function ($query) {
                return $query->recent();
            })
            ->paginate(15)
            ->withQueryString();

        $topUsers = User::withCount(['communityPosts', 'followers'])
            ->whereHas('communityPosts', function ($query) {
                $query->where('is_approved', true);
            })
            ->orderByDesc('community_posts_count')
            ->limit(5)
            ->get();

        $popularTags = CommunityPost::select('tags')
            ->whereNotNull('tags')
            ->where('is_approved', true)
            ->get()
            ->flatMap(fn($post) => $post->tags ?? [])
            ->countBy()
            ->sortDesc()
            ->take(10)
            ->keys();

        return Inertia::render('Frontpages/Community/Index', [
            'posts' => $posts,
            'topUsers' => $topUsers,
            'popularTags' => $popularTags,
            'filters' => [
                'type' => $type,
                'sort' => $sort,
                'search' => $search,
            ],
            'isAuthenticated' => auth()->check(),
        ]);
    }

    public function creates()
    {
        $user = auth()->user();
        $courses = $user->courses()->active()->get();

        return Inertia::render('Frontpages/Community/Create', [
            'courses' => $courses,
        ]);
    }
}