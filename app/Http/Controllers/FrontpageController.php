<?php
// app/Http/Controllers/FrontpageController.php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\SubscriptionPlan;
use Inertia\Inertia;
use Illuminate\Http\Request;

class FrontpageController extends Controller
{
    public function features()
    {
        return Inertia::render('Frontpages/Features', [
            'meta' => [
                'title' => 'Powerful Features for Smart Learning',
                'description' => 'Discover how Olilearn combines artificial intelligence with proven learning methodologies to create the most effective and engaging educational experience.',
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ]
        ]);
    }

    public function community()
    {
        return Inertia::render('Frontpages/Community', [
            'meta' => [
                'title' => 'OliLearn - Learn Anything With AI',
                'description' => 'Your AI tutor for any subject.',
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ]
        ]);
    }

    public function about()
    {
        return Inertia::render('Frontpages/About', [
            'meta' => [
                'title' => 'Revolutionizing Education with AI',
                'description' => 'We believe everyone deserves access to personalized, effective learning. Olilearn combines cutting-edge artificial intelligence with proven educational methodologies to make this vision a reality.',
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ]
        ]);
    }

    public function pricing()
{
    $subscriptionPlans = SubscriptionPlan::active()
        ->orderBy('sort_order')
        ->get()
        ->map(function ($plan) {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'code' => $plan->code,
                'description' => $plan->description,
                'price' => $plan->price,
                'currency' => $plan->currency,
                'monthly_price' => $plan->monthly_price,
                'yearly_price' => $plan->yearly_price,
                'features' => $plan->features,
                'max_courses' => $plan->max_courses,
                'max_ai_requests_per_month' => $plan->max_ai_requests_per_month,
                'ai_grading' => $plan->ai_grading,
                'priority_support' => $plan->priority_support,
                'is_active' => $plan->is_active,
                'is_popular' => $plan->is_popular,
                'sort_order' => $plan->sort_order,
                'role' => $plan->role,
                'tier' => $plan->tier,
                'is_free' => $plan->isFree(),
                'recommended_features' => $plan->getRecommendedFeatures(),
            ];
        });

    return Inertia::render('Frontpages/Pricing', [
        'subscriptionPlans' => $subscriptionPlans, 'meta' => [
                'title' => 'Choose Your Perfect Plan',
                'description' => 'Select the ideal plan for your learning journey. All plans include our core AI features and personalized learning tools.',
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ]
        ]
        );
}


    public function help()
    {
        return Inertia::render('Frontpages/Help', [
            'meta' => [
                'title' => 'OliLearn - Learn Anything With AI',
                'description' => 'Your AI tutor for any subject.',
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ]
        ]);
    }

    public function contact()
    {
        return Inertia::render('Frontpages/Contact', [
            'meta' => [
                'title' => 'OliLearn - Learn Anything With AI',
                'description' => 'Your AI tutor for any subject.',
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ]
        ]);
    }

    public function faq()
    {
        return Inertia::render('Frontpages/Faq', [
            'meta' => [
                'title' => 'OliLearn - Learn Anything With AI',
                'description' => 'Your AI tutor for any subject.',
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ]
        ]);
    }

    public function enterprise()
    {
        return Inertia::render('Frontpages/Enterprise', [
            'meta' => [
                'title' => 'OliLearn - Learn Anything With AI',
                'description' => 'Your AI tutor for any subject.',
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ]
        ]);
    }

    /**
     * Display blog index page
     */
    public function blogIndex(Request $request)
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

        $posts = $query->paginate(12);

        return Inertia::render('Frontpages/Blog/Index', [
            'posts' => $posts,
            'filters' => $request->only(['search', 'category']),
            'categories' => BlogPost::published()->distinct()->pluck('category'),
            'meta' => [
                'title' => 'Our Blog',
                'description' => 'Insights, tips, and news about AI-powered learning and education technology',
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ],
            'featuredPosts' => BlogPost::with('author')
                ->published()
                ->orderBy('published_at', 'desc')
                ->limit(3)
                ->get()
        ]);
    }

    /**
     * Display blog post show page
     */
    public function blogShow($slug)
    {
        $post = BlogPost::with('author')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Get related posts
        $relatedPosts = BlogPost::with('author')
            ->published()
            ->where('id', '!=', $post->id)
            ->where('category', $post->category)
            ->limit(3)
            ->get();

        // Get popular posts
        $popularPosts = BlogPost::with('author')
            ->published()
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        return Inertia::render('Frontpages/Blog/Show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'popularPosts' => $popularPosts,
            'meta' => [
                'title' => $post->title,
                'description' => $post->excerpt,
                'image' => '/storage/'. $post->image_url,
                'url' => url()->current(),
            ]
        ]);
    }

    /**
     * Display courses index page
     */
    public function coursesIndex(Request $request)
    {
        $query = Course::with(['modules', 'examBoard'])
            ->active()
            ->withProgress();

        // Filter by subject
        if ($request->has('subject')) {
            $query->bySubject($request->subject);
        }

        // Filter by level
        if ($request->has('level')) {
            $query->byLevel($request->level);
        }

        $courses = $query->paginate(12);

        return Inertia::render('Frontpages/Courses/Index', [
            'courses' => $courses,
            'filters' => $request->only(['subject', 'level']),
            'subjects' => Course::active()->distinct()->pluck('subject'),
            'levels' => Course::active()->distinct()->pluck('level'),
            'meta' => [
                'title' => 'Explore Courses',
                'description' => 'Discover our AI-powered courses designed to help you learn smarter and faster.',
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ]
        ]);
    }

    /**
     * Display course show page
     */
    public function courseShow($id)
    {
        $course = Course::with(['modules', 'examBoard', 'modules.topics'])
            ->withProgress()
            ->findOrFail($id);

        $relatedCourses = Course::with(['modules'])
            ->active()
            ->where('id', '!=', $course->id)
            ->where('subject', $course->subject)
            ->limit(3)
            ->get();

        return Inertia::render('Frontpages/Courses/Show', [
            'course' => $course,
            'relatedCourses' => $relatedCourses,'meta' => [
                'title' => $course->title,
                'description' => $course->description,
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ]
        ]);
    }
}
