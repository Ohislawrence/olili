<?php
// app/Http/Controllers/WelcomeController.php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\BlogPost;
use App\Models\SubscriptionPlan;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    /**
     * Display the welcome page.
     */
    public function index()
    {
        // Get featured courses with progress information
        $courses = Course::where('visibility', 'public')->with(['modules', 'examBoard'])
            ->limit(6)
            ->latest()
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'subject' => $course->subject,
                    'description' => $course->description,
                    'level' => $course->level,
                    'estimated_duration_hours' => $course->estimated_duration_hours,
                    'modules_count' => $course->modules->count(),
                    'status' => $course->status,
                    'slug' => $course->slug,
                ];
            });

        // Get latest blog posts
        $blogPosts = BlogPost::with('author')
            ->published()
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->getExcerpt(),
                    'content' => $post->content,
                    'image_url' => $post->image_url,
                    'category' => $post->category,
                    'published_at' => $post->published_at?->toISOString(),
                    'author' => $post->author ? [
                        'name' => $post->author->name,
                        'email' => $post->author->email,
                    ] : null,
                ];
            });

        // Get subscription plans
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

        return Inertia::render('Frontpages/Welcome', [
            'courses' => $courses,
            'blogPosts' => $blogPosts,
            'subscriptionPlans' => $subscriptionPlans,
            'meta' => [
                'title' => 'Unlocking potential with AI-personalized learning for every student',
                'description' => 'Discover how Olilearn combines artificial intelligence with proven learning methodologies to create the most effective and engaging educational experience.',
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ]
        ]);
    }
}

