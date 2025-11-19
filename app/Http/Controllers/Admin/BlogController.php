<?php

namespace App\Http\Controllers\Admin;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class BlogController extends Controller

{
    /**
     * Display a listing of the blog posts.
     */
    public function index(Request $request)
    {
        $query = BlogPost::with('author')
            ->published()
            ->orderBy('published_at', 'desc');

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        $posts = $query->paginate(9);

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'filters' => $request->only(['search', 'category']),
            'categories' => BlogPost::published()->distinct()->pluck('category'),
        ]);
    }

    /**
     * Show the form for creating a new blog post.
     */
    public function create()
    {
        return Inertia::render('Blog/Create');
    }

    /**
     * Store a newly created blog post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'category' => 'required|string|max:100',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|array',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image_url'] = $request->file('image')->store('blog-images', 'public');
        }

        // Generate slug
        $validated['slug'] = Str::slug($validated['title']);

        // Set author
        $validated['author_id'] = auth()->id();

        BlogPost::create($validated);

        return redirect()->route('blog.index')
            ->with('success', 'Blog post created successfully.');
    }

    /**
     * Display the specified blog post.
     */
    public function show(BlogPost $blog)
    {
        // If post is not published and user is not author, show 404
        if (!$blog->isPublished() && $blog->author_id !== auth()->id()) {
            abort(404);
        }

        $blog->load('author');

        // Get related posts
        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $blog->id)
            ->where('category', $blog->category)
            ->limit(3)
            ->get();

        return Inertia::render('Blog/Show', [
            'post' => $blog,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    /**
     * Show the form for editing the blog post.
     */
    public function edit(BlogPost $blog)
    {
        return Inertia::render('Blog/Edit', [
            'post' => $blog,
        ]);
    }

    /**
     * Update the specified blog post.
     */
    public function update(Request $request, BlogPost $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'category' => 'required|string|max:100',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|array',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($blog->image_url) {
                Storage::disk('public')->delete($blog->image_url);
            }
            $validated['image_url'] = $request->file('image')->store('blog-images', 'public');
        }

        // Update slug if title changed
        if ($blog->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $blog->update($validated);

        return redirect()->route('blog.index')
            ->with('success', 'Blog post updated successfully.');
    }

    /**
     * Remove the specified blog post.
     */
    public function destroy(BlogPost $blog)
    {
        // Delete associated image
        if ($blog->image_url) {
            Storage::disk('public')->delete($blog->image_url);
        }

        $blog->delete();

        return redirect()->route('blog.index')
            ->with('success', 'Blog post deleted successfully.');
    }

    /**
     * Get featured blog posts for homepage
     */
    public function featured()
    {
        $posts = BlogPost::with('author')
            ->published()
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return response()->json($posts);
    }
}
