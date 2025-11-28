<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlogPost::with('author')
            ->latest();

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $blogPosts = $query->paginate(12);

        return Inertia::render('Admin/BlogPosts/Index', [
            'blog_posts' => $blogPosts,
            'filters' => $request->only(['search', 'category', 'status']),
            'categories' => $this->getCategories(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/BlogPosts/Create', [
            'categories' => $this->getCategories(),
            'authors' => User::role('admin')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'required|string|max:255',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $request->file('image')->store('blog-images', 'public');
        }

        BlogPost::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'image_url' => $imageUrl,
            'category' => $request->category,
            'published_at' => $request->published_at ?? now(),
            'is_published' => $request->is_published ?? false,
            'author_id' => auth()->id(),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'tags' => $request->tags ?? [],
        ]);

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Blog post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogPost $blogPost)
    {
        $blogPost->load('author');

        return Inertia::render('Admin/BlogPosts/Show', [
            'blog_post' => $blogPost,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogPost $blogPost)
    {
        return Inertia::render('Admin/BlogPosts/Edit', [
            'blog_post' => $blogPost,
            'categories' => $this->getCategories(),
            'authors' => User::role('admin')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogPost $blogPost)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $blogPost->id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'required|string|max:255',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255',
        ]);

        $imageUrl = $blogPost->image_url;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($blogPost->image_url) {
                Storage::disk('public')->delete($blogPost->image_url);
            }
            $imageUrl = $request->file('image')->store('blog-images', 'public');
        }

        $blogPost->update([
            'title' => $request->title,
            'slug' => $request->slug,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'image_url' => $imageUrl,
            'category' => $request->category,
            'published_at' => $request->published_at,
            'is_published' => $request->is_published ?? false,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'tags' => $request->tags ?? [],
        ]);

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Blog post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $blogPost)
    {
        // Delete image if exists
        if ($blogPost->image_url) {
            Storage::disk('public')->delete($blogPost->image_url);
        }

        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Blog post deleted successfully.');
    }

    /**
     * Toggle publish status
     */
    public function togglePublish(BlogPost $blogPost)
    {
        $blogPost->update([
            'is_published' => !$blogPost->is_published,
            'published_at' => $blogPost->is_published ? null : now(),
        ]);

        return back()->with('success', 'Blog post status updated successfully.');
    }

    /**
     * Get available categories
     */
    private function getCategories()
    {
        return [
            'Technology',
            'Education',
            'AI & Machine Learning',
            'Study Tips',
            'Career Guidance',
            'Online Learning',
            'Student Life',
            'Tutoring',
            'Product Updates',
            'Success Stories',
        ];
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        $path = $request->file('image')->store('blog-images', 'public');
        
        return response()->json([
            'url' => Storage::disk('public')->url($path)
        ]);
    }
}