<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\PostComment;
use App\Models\PostLike;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function show(CommunityPost $post)
    {
        // Only show approved posts to guests
        if (!$post->is_approved && !auth()->check()) {
            abort(404);
        }

        $post->load(['user', 'course', 'comments.user', 'comments.replies.user']);
        $post->incrementViews();

        $relatedPosts = CommunityPost::with('user')
            ->where('id', '!=', $post->id)
            ->where('is_approved', true)
            ->where(function ($query) use ($post) {
                $query->where('course_id', $post->course_id)
                    ->orWhere('user_id', $post->user_id);
            })
            ->limit(5)
            ->get();

        $isLiked = auth()->check() ? $post->likes()->where('user_id', auth()->id())->exists() : false;
        $isFollowing = auth()->check() ? auth()->user()->isFollowing($post->user) : false;

        return Inertia::render('Frontpages/Community/Show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'isLiked' => $isLiked,
            'isFollowing' => $isFollowing,
            'isAuthenticated' => auth()->check(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:discussion,question,achievement,resource',
            'course_id' => 'nullable|exists:courses,id',
            'tags' => 'nullable|array',
        ]);

        $post = CommunityPost::create([
            'user_id' => auth()->id(),
            'course_id' => $request->course_id,
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'tags' => $request->tags,
        ]);

        return redirect()->route('community.posts.show', $post->id)
            ->with('success', 'Post created successfully!');
    }

    public function update(Request $request, CommunityPost $post)
    {
        //$this->authorize('update', $post);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'nullable|array',
        ]);

        $post->update($request->only(['title', 'content', 'tags']));

        return back()->with('success', 'Post updated successfully!');
    }

    public function destroy(CommunityPost $post)
    {
        //$this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('community.index')
            ->with('success', 'Post deleted successfully!');
    }

    public function like(CommunityPost $post)
    {
        $like = PostLike::firstOrCreate([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
        ]);

        if ($like->wasRecentlyCreated) {
            $post->increment('like_count');
        }

        return back()->with('success', 'Post liked!');
    }

    public function unlike(CommunityPost $post)
    {
        $deleted = PostLike::where([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
        ])->delete();

        if ($deleted) {
            $post->decrement('like_count');
        }

        return back()->with('success', 'Post unliked!');
    }

    public function storeComment(Request $request, CommunityPost $post)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:post_comments,id',
        ]);

        PostComment::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        $post->increment('comment_count');

        return back()->with('success', 'Comment added!');
    }

    public function deleteComment(PostComment $comment)
    {
        //$this->authorize('delete', $comment);

        $comment->delete();
        $comment->post->decrement('comment_count');

        return back()->with('success', 'Comment deleted!');
    }

    public function create()
    {
        $user = auth()->user();
        $courses = $user->courses()->active()->get();

        return Inertia::render('Frontpages/Community/Create', [
            'courses' => $courses,
        ]);
    }
}
