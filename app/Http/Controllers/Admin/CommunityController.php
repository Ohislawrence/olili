<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $query = CommunityPost::with(['user', 'course', 'moderator'])
            ->latest();

        // Filters
        $filters = $request->only(['search', 'status', 'type', 'user']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            switch ($status) {
                case 'pending':
                    $query->where('is_approved', false);
                    break;
                case 'approved':
                    $query->where('is_approved', true);
                    break;
                case 'flagged':
                    $query->where('is_approved', false)->whereNotNull('moderation_notes');
                    break;
            }
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($user = $request->input('user')) {
            $query->where('user_id', $user);
        }

        $posts = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => CommunityPost::count(),
            'approved' => CommunityPost::where('is_approved', true)->count(),
            'pending' => CommunityPost::where('is_approved', false)->count(),
            'today' => CommunityPost::whereDate('created_at', today())->count(),
        ];

        $recentUsers = User::has('communityPosts')
            ->withCount('communityPosts')
            ->orderByDesc('community_posts_count')
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Community/Index', [
            'posts' => $posts,
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'filters' => $filters,
        ]);
    }

    public function show(CommunityPost $post)
    {
        $post->load([
            'user',
            'course',
            'comments.user',
            'comments.replies.user',
            'likes.user',
            'moderator'
        ]);

        $relatedPosts = CommunityPost::with('user')
            ->where('user_id', $post->user_id)
            ->where('id', '!=', $post->id)
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Community/Show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    public function approve(CommunityPost $post)
    {
        $post->update([
            'is_approved' => true,
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
            'moderation_notes' => 'Approved by admin',
        ]);

        return back()->with('success', 'Post approved successfully.');
    }

    public function reject(CommunityPost $post, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $post->update([
            'is_approved' => false,
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
            'moderation_notes' => $request->reason,
        ]);

        // Optionally notify user about rejection
        // Notification::send($post->user, new PostRejected($post, $request->reason));

        return back()->with('success', 'Post rejected successfully.');
    }

    public function pin(CommunityPost $post)
    {
        $post->update(['is_pinned' => true]);
        return back()->with('success', 'Post pinned successfully.');
    }

    public function unpin(CommunityPost $post)
    {
        $post->update(['is_pinned' => false]);
        return back()->with('success', 'Post unpinned successfully.');
    }

    public function destroy(CommunityPost $post)
    {
        $post->delete();

        return redirect()->route('admin.community.index.admin')
            ->with('success', 'Post deleted successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete,pin,unpin',
            'ids' => 'required|array',
            'ids.*' => 'exists:community_posts,id',
        ]);

        $action = $request->action;
        $ids = $request->ids;

        switch ($action) {
            case 'approve':
                CommunityPost::whereIn('id', $ids)->update([
                    'is_approved' => true,
                    'moderated_by' => auth()->id(),
                    'moderated_at' => now(),
                ]);
                $message = 'Posts approved successfully.';
                break;

            case 'reject':
                CommunityPost::whereIn('id', $ids)->update([
                    'is_approved' => false,
                    'moderated_by' => auth()->id(),
                    'moderated_at' => now(),
                    'moderation_notes' => 'Bulk rejected by admin',
                ]);
                $message = 'Posts rejected successfully.';
                break;

            case 'pin':
                CommunityPost::whereIn('id', $ids)->update(['is_pinned' => true]);
                $message = 'Posts pinned successfully.';
                break;

            case 'unpin':
                CommunityPost::whereIn('id', $ids)->update(['is_pinned' => false]);
                $message = 'Posts unpinned successfully.';
                break;

            case 'delete':
                CommunityPost::whereIn('id', $ids)->delete();
                $message = 'Posts deleted successfully.';
                break;
        }

        return back()->with('success', $message);
    }

    public function moderateComments(Request $request)
    {
        $query = PostComment::with(['user', 'post'])
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('is_approved', $status === 'approved');
        }

        $comments = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Community/Comments', [
            'comments' => $comments,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function approveComment(PostComment $comment)
    {
        $comment->update(['is_approved' => true]);
        return back()->with('success', 'Comment approved successfully.');
    }

    public function deleteComment(PostComment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully.');
    }

    public function userActivity(User $user)
    {
        $user->loadCount(['communityPosts', 'postComments', 'postLikes']);

        $posts = $user->communityPosts()
            ->with(['course', 'comments'])
            ->latest()
            ->paginate(10);

        $comments = $user->postComments()
            ->with('post')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Community/UserActivity', [
            'user' => $user,
            'posts' => $posts,
            'comments' => $comments,
            'stats' => [
                'total_posts' => $user->community_posts_count,
                'total_comments' => $user->post_comments_count,
                'total_likes' => $user->post_likes_count,
                'avg_post_likes' => $user->communityPosts()->avg('like_count') ?? 0,
                'avg_post_comments' => $user->communityPosts()->avg('comment_count') ?? 0,
            ],
        ]);
    }

    public function export(Request $request)
    {
        $posts = CommunityPost::with(['user', 'course'])
            ->when($request->has('start_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->has('end_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->end_date);
            })
            ->when($request->has('status'), function ($query) use ($request) {
                $query->where('is_approved', $request->status === 'approved');
            })
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="community_posts_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($posts) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'ID', 'Title', 'User', 'Email', 'Type', 'Status',
                'Likes', 'Comments', 'Views', 'Created At', 'Approved',
                'Moderator', 'Moderated At'
            ]);

            // Data
            foreach ($posts as $post) {
                fputcsv($file, [
                    $post->id,
                    $post->title,
                    $post->user->name,
                    $post->user->email,
                    $post->type,
                    $post->is_approved ? 'Approved' : 'Pending',
                    $post->like_count,
                    $post->comment_count,
                    $post->views,
                    $post->created_at->format('Y-m-d H:i:s'),
                    $post->is_approved ? 'Yes' : 'No',
                    $post->moderator?->name,
                    $post->moderated_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
