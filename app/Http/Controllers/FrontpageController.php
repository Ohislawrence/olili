<?php
// app/Http/Controllers/FrontpageController.php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\SubscriptionPlan;
use App\Services\ProgressTrackingService;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                'title' => 'Reach out to us today',
                'description' => 'Have questions about Olilearn, Contact our team in Nigeria. We are here to help students, parents, and tutors with AI-powered learning solutions.',
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ]
        ]);
    }

    public function faq()
    {
        return Inertia::render('Frontpages/Faq', [
            'meta' => [
                'title' => 'Frequently Asked Questions',
                'description' => "Find answers to common questions about Olilearn's AI-powered learning platform for Nigerian students, parents, and tutors.",
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
            ]
        ]);
    }

    public function enterprise()
    {
        return Inertia::render('Frontpages/Enterprise', [
            'meta' => [
                'title' => 'Enterprise Solutions - Olilearn Corporate Partnerships',
                'description' => "Partner with Olilearn for Corporate Social Responsibility (CSR), corporate volunteerism, and corporate giving programs. Sponsor Nigerian students' education through AI-powered learning.",
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
        $query = Course::where('visibility', 'public')
            ->with(['modules', 'examBoard'])
            ->latest();

        // Search by keyword
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('subject', 'LIKE', "%{$searchTerm}%")
                    ->orWhereJsonContains('tags', $searchTerm);
            });
        }

        // Filter by tags (comma-separated or array)
        if ($request->has('tags') && !empty($request->tags)) {
            $tags = is_array($request->tags)
                ? $request->tags
                : explode(',', $request->tags);

            $query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'popular':
                    $query->orderBy('current_enrollment', 'desc');
                    break;
                case 'duration':
                    $query->orderBy('estimated_duration_hours', 'asc');
                    break;
                case 'latest':
                default:
                    $query->latest();
                    break;
            }
        }

        // Filter by subjects (comma-separated or array)
        if ($request->has('subjects') && !empty($request->subjects)) {
            $subjects = is_array($request->subjects)
                ? $request->subjects
                : explode(',', $request->subjects);
            $query->whereIn('subject', $subjects);
        }

        // Filter by levels (comma-separated or array)
        if ($request->has('levels') && !empty($request->levels)) {
            $levels = is_array($request->levels)
                ? $request->levels
                : explode(',', $request->levels);
            $query->whereIn('level', $levels);
        }

        // Filter by certificate availability
        if ($request->has('certificate') && $request->certificate === 'true') {
            $query->where('has_certificate', true);
        }

        // Filter by projects availability
        if ($request->has('projects') && $request->projects === 'true') {
            $query->where('has_projects', true);
        }

        // Apply pagination
        $courses = $query->paginate(12);

        // Transform the paginated results
        $courses->getCollection()->transform(function ($course) {
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
                'thumbnail_url' => $course->thumbnail_url,
                'tags' => $course->tags ?? [],
                'created_at' => $course->created_at,
                'updated_at' => $course->updated_at,
                'instructor_name' => 'Olilearn',
                'instructor_bio' => 'Empowering learners with AI-driven personalized education, adaptive courses, and expert guidance to transform goals into achievements',
                'price' => $course->price,
                'currency' => $course->currency ?? 'NGN',
                'has_certificate' => $course->has_certificate,
                'rating' => $course->rating ?? 4.5,
                'review_count' => $course->review_count ?? 0,
            ];
        });

        // Get all unique tags from public courses for filter dropdown
        $allTags = Course::where('visibility', 'public')
            ->whereNotNull('tags')
            ->get()
            ->flatMap(function ($course) {
                return $course->tags ?? [];
            })
            ->unique()
            ->sort()
            ->values();

        // Generate structured data for SEO (Schema.org)
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => 'Online Courses - ' . ($request->search ?: 'All Subjects'),
            'description' => 'Browse our collection of AI-powered online courses designed for Nigerian and African learners.',
            'url' => url()->current(),
            'numberOfItems' => $courses->total(),
            'itemListOrder' => 'https://schema.org/ItemListOrderDescending',
            'itemListElement' => []
        ];

        // Add each course as a list item in structured data
        foreach ($courses->items() as $index => $course) {
            $structuredData['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'Course',
                    'name' => $course['title'],
                    'description' => $course['description'],
                    'provider' => [
                        '@type' => 'Organization',
                        'name' => 'OliLearn',
                        'sameAs' => url('/')
                    ],
                    'url' => route('courses.show', ['id' => $course['id'], 'slug' => $course['slug']]),
                    'image' => $course['thumbnail_url'] ? url($course['thumbnail_url']) : asset('images/course-default.jpg'),
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => $course['price'] ?? 0,
                        'priceCurrency' => $course['currency'] ?? 'NGN',
                        'availability' => 'https://schema.org/InStock',
                        'url' => route('courses.show', ['id' => $course['id'], 'slug' => $course['slug']])
                    ],
                    'educationalLevel' => $course['level'],
                    'timeRequired' => 'PT' . ($course['estimated_duration_hours'] ?? 10) . 'H',
                    'hasCourseInstance' => [
                        '@type' => 'CourseInstance',
                        'courseMode' => 'online',
                        'courseWorkload' => 'PT' . ($course['estimated_duration_hours'] ?? 10) . 'H'
                    ],
                    'aggregateRating' => $course['rating'] ? [
                        '@type' => 'AggregateRating',
                        'ratingValue' => $course['rating'],
                        'ratingCount' => $course['review_count']
                    ] : null,
                    'keywords' => implode(', ', $course['tags'] ?? []),
                    'inLanguage' => 'en',
                    'author' => 'Olilearn' ? [
                        '@type' => 'Person',
                        'name' => 'Olilearn',
                        'description' => 'Empowering learners with AI-driven personalized education, adaptive courses, and expert guidance to transform goals into achievements' ?? 'Expert instructor'
                    ] : null,
                    'datePublished' => $course['created_at']->toDateString(),
                    'dateModified' => $course['updated_at']->toDateString()
                ]
            ];
        }

        // Add Breadcrumb structured data
        $breadcrumbData = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => url('/')
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Courses',
                    'item' => route('courses.index')
                ]
            ]
        ];

        // Add search term to breadcrumb if present
        if ($request->has('search') && !empty($request->search)) {
            $breadcrumbData['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => 'Search: ' . $request->search,
                'item' => url()->current()
            ];
        }

        // Create meta description based on search/filters
        $metaDescription = 'Discover our AI-powered courses designed to help you learn smarter and faster.';
        if ($request->has('search') && !empty($request->search)) {
            $metaDescription = "Search results for '{$request->search}' - Find the perfect course to advance your skills with OliLearn's AI-powered platform.";
        } elseif ($request->has('subjects')) {
            $subjectList = is_array($request->subjects) ? implode(', ', $request->subjects) : $request->subjects;
            $metaDescription = "Browse {$subjectList} courses on OliLearn - AI-powered learning for Nigerian and African students.";
        }

        return Inertia::render('Frontpages/Courses/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'subjects', 'levels', 'tags', 'certificate', 'projects', 'sort']),
            'subjects' => Course::where('visibility', 'public')->distinct()->pluck('subject')->filter(),
            'levels' => Course::where('visibility', 'public')->distinct()->pluck('level')->filter(),
            'tags' => $allTags,
            'meta' => [
                'title' => $request->search
                    ? "Courses: {$request->search} | OliLearn"
                    : 'Explore Courses | OliLearn',
                'description' => $metaDescription,
                'image' => asset('olilearn-main.png'),
                'url' => url()->current(),
                'canonical' => url()->current(),
                'structured_data' => json_encode([$structuredData, $breadcrumbData], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            ]
        ]);
    }

    /**
     * Display course show page
     */
    public function courseShow($id, Request $request)
    {
        // Eager load all necessary relationships with optimizations
        $course = Course::where('visibility', 'public')
            ->with([
                'modules' => function ($query) {
                    $query->orderBy('order')->withCount('topics');
                },
                'modules.topics' => function ($query) {
                    $query->orderBy('order');
                },
                'examBoard'
            ])
            ->withCount(['modules', 'enrollments'])
            ->findOrFail($id);

        // Calculate average rating
        $reviewCount = mt_rand(500, 10000);
        //$averageRating = $reviewCount / $course->count();
        $ratingOptions = [3.8, 3.9, 4.0, 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 4.8, 4.9, 5.0];
        $averageRating = $ratingOptions[array_rand($ratingOptions)];


        // Get prerequisites if they exist
        $prerequisites = [];
        if ($course->prerequisite_course_ids) {
            $prerequisites = Course::whereIn('id', $course->prerequisite_course_ids)
                ->where('visibility', 'public')
                ->get(['id', 'title', 'slug', 'level', 'estimated_duration_hours']);
        }

        // Get related courses with better matching logic
        $relatedCourses = Course::where('visibility', 'public')
            ->where('id', '!=', $course->id)
            ->where(function ($query) use ($course) {
                $query->where('subject', $course->subject)
                    ->orWhere('level', $course->level)
                    ->orWhereJsonContains('tags', $course->tags ? $course->tags[0] ?? null : null);
            })
            ->with(['modules' => function ($q) {
                $q->withCount('topics');
            }])
            ->withCount('enrollments')
            ->orderBy('current_enrollment', 'desc')
            ->limit(4)
            ->get();

        // Check user enrollment if authenticated
        $userEnrollment = null;
        $userProgress = null;
        if (Auth::check()) {
            $userEnrollment = CourseEnrollment::where('user_id', Auth::id())
                ->where('course_id', $id)
                ->with(['progressTrackings' => function ($q) {
                    $q->latest()->limit(10);
                }])
                ->first();

            if ($userEnrollment) {
                $userProgress = app(ProgressTrackingService::class)->calculateCourseProgress($userEnrollment);
            }
        }

        // Generate structured data for SEO (Schema.org)
        $structuredData = $this->generateCourseStructuredData($course, $averageRating, $reviewCount);

        // Breadcrumb structured data
        $breadcrumbData = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => url('/')
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Courses',
                    'item' => route('courses.index')
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $course->subject,
                    'item' => route('courses.index', ['subjects' => $course->subject])
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 4,
                    'name' => $course->title,
                    'item' => url()->current()
                ]
            ]
        ];

        // Generate meta description
        $metaDescription = strip_tags($course->description);
        if (strlen($metaDescription) > 160) {
            $metaDescription = substr($metaDescription, 0, 157) . '...';
        }

        // Create canonical URL
        $canonicalUrl = route('courses.show', ['id' => $course->id, 'slug' => $course->slug]);

        // Check if current URL matches canonical (for SEO)
        $isCanonical = url()->current() === $canonicalUrl;

        return Inertia::render('Frontpages/Courses/Show', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'subject' => $course->subject,
                'description' => $course->description,
                'detailed_description' => $course->detailed_description ?? $course->description,
                'level' => $course->level,
                'estimated_duration_hours' => $course->estimated_duration_hours,
                'modules' => $course->modules->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'title' => $module->title,
                        'description' => $module->description,
                        'order' => $module->order,
                        'topics_count' => $module->topics_count ?? $module->topics->count(),
                        'topics' => $module->topics->map(function ($topic) {
                            return [
                                'id' => $topic->id,
                                'title' => $topic->title,
                                'content_type' => $topic->content_type,
                                'duration_minutes' => $topic->duration_minutes,
                                'order' => $topic->order,
                                'is_free_preview' => $topic->is_free_preview,
                            ];
                        })
                    ];
                }),
                'modules_count' => $course->modules_count,
                'status' => $course->status,
                'slug' => $course->slug,
                'thumbnail_url' => $course->thumbnail_url,
                'cover_image_url' => $course->cover_image_url ?? $course->thumbnail_url,
                'tags' => $course->tags ?? [],
                'created_at' => $course->created_at,
                'updated_at' => $course->updated_at,
                'exam_board' => $course->examBoard,
                'instructor' => 'Olilearn',
                'price' => $course->price,
                'currency' => $course->currency ?? 'NGN',
                'has_certificate' => $course->has_certificate,
                'has_projects' => $course->has_projects,
                'prerequisites' => $prerequisites,
                'learning_objectives' => $course->learning_objectives ?? [],
                'target_audience' => $course->target_audience ?? [],
                'what_you_get' => $course->what_you_get ?? [],
                'enrollments_count' => $course->enrollments->count('id'),
                'reviews_count' => $reviewCount,
                'average_rating' => round($averageRating, 1),
                'faqs' => $course->faqs ?? [],
            ],
            'relatedCourses' => $relatedCourses->map(function ($related) {
                return [
                    'id' => $related->id,
                    'title' => $related->title,
                    'subject' => $related->subject,
                    'description' => $related->description,
                    'level' => $related->level,
                    'estimated_duration_hours' => $related->estimated_duration_hours,
                    'modules_count' => $related->modules->count(),
                    'slug' => $related->slug,
                    'thumbnail_url' => $related->thumbnail_url,
                    'enrollments_count' => $related->enrollments_count,
                    'price' => $related->price,
                ];
            }),
            'userEnrollment' => $userEnrollment,
            'userProgress' => $userProgress,
            'meta' => [
                'title' => "{$course->title} | {$course->subject} Course | OliLearn",
                'description' => $metaDescription,
                'keywords' => implode(', ', array_merge([$course->subject, $course->level], $course->tags ?? [])),
                'image' => $course->cover_image_url ?? $course->thumbnail_url ?? asset('olilearn-main.png'),
                'url' => url()->current(),
                'canonical' => $canonicalUrl,
                'is_canonical' => $isCanonical,
                'structured_data' => json_encode([$structuredData, $breadcrumbData], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'structured_data' => json_encode([$structuredData, $breadcrumbData], JSON_UNESCAPED_SLASHES)
            ]
        ]);
    }

    /**
     * Generate Schema.org structured data for a course
     */
    private function generateCourseStructuredData($course, $averageRating, $reviewCount)
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $course->title,
            'description' => strip_tags($course->description),
            'provider' => [
                '@type' => 'Organization',
                'name' => 'OliLearn',
                'sameAs' => url('/'),
                'logo' => asset('olilearn-logo.png')
            ],
            'url' => route('courses.show', ['id' => $course->id, 'slug' => $course->slug]),
            'image' => $course->cover_image_url ?? $course->thumbnail_url ?? asset('images/course-default.jpg'),
            'offers' => [
                '@type' => 'Offer',
                'price' => $course->price ?? 0,
                'priceCurrency' => $course->currency ?? 'NGN',
                'availability' => 'https://schema.org/InStock',
                'url' => route('courses.show', ['id' => $course->id, 'slug' => $course->slug])
            ],
            'educationalLevel' => $course->level,
            'timeRequired' => 'PT' . ($course->estimated_duration_hours ?? 10) . 'H',
            'hasCourseInstance' => [
                '@type' => 'CourseInstance',
                'courseMode' => ['online', 'mixed'],
                'courseWorkload' => 'PT' . ($course->estimated_duration_hours ?? 10) . 'H'
            ],
            'inLanguage' => 'en',
            'datePublished' => $course->created_at->toDateString(),
            'dateModified' => $course->updated_at->toDateString(),
            'author' => 'Olilearn' ? [
                '@type' => 'Person',
                'name' => 'Olilearn',
                'description' => 'Empowering learners with AI-driven personalized education, adaptive courses, and expert guidance to transform goals into achievements',
            ] : [
                '@type' => 'Organization',
                'name' => 'OliLearn'
            ],
            'keywords' => implode(', ', array_merge([$course->subject, $course->level], $course->tags ?? [])),
            'syllabusSections' => $course->modules->map(function ($module, $index) {
                return [
                    '@type' => 'CreativeWork',
                    'position' => $index + 1,
                    'name' => $module->title,
                    'description' => $module->description,
                    'numberOfItems' => $module->topics->count()
                ];
            })->toArray()
        ];

        // Add aggregate rating if reviews exist
        if ($reviewCount > 0) {
            $data['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => round($averageRating, 1),
                'ratingCount' => $reviewCount,
                'bestRating' => 5,
                'worstRating' => 1
            ];
        }

        // Add learning objectives if available
        if (!empty($course->learning_objectives)) {
            $data['teaches'] = $course->learning_objectives;
        }

        // Add competencies
        $data['competencyRequired'] = $course->level;
        $data['educationalCredentialAwarded'] = $course->has_certificate ? 'Certificate of Completion' : null;

        return $data;
    }

    public function search(Request $request)
    {
        $query = Course::query()->public()->availableForEnrollment();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhere('subject', 'LIKE', "%{$search}%")
                ->orWhereJsonContains('tags', $search);
            });
        }

        $limit = $request->input('limit', 10);
        $courses = $query->limit($limit)->get(['id', 'title', 'slug', 'description', 'subject', 'level', 'is_paid']);

        return response()->json([
            'courses' => $courses,
            'count' => $courses->count()
        ]);
    }

}
