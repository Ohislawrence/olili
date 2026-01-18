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



    public function waeclanding()
    {
        // Get WAEC-specific courses with proper filtering
        $courses = Course::where('visibility', 'public')
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('subject', 'LIKE', '%WAEC%')
                    ->orWhere('title', 'LIKE', '%WAEC%')
                    ->orWhere('tags', 'LIKE', '%WAEC%')
                    ->orWhereHas('examBoard', function($q) {
                        $q->where('name', 'LIKE', '%WAEC%');
                    })
                    ->orWhere('subject', 'LIKE', '%Mathematics%')
                    ->orWhere('subject', 'LIKE', '%English%')
                    ->orWhere('subject', 'LIKE', '%Biology%')
                    ->orWhere('subject', 'LIKE', '%Chemistry%')
                    ->orWhere('subject', 'LIKE', '%Physics%')
                    ->orWhere('subject', 'LIKE', '%Economics%')
                    ->orWhere('subject', 'LIKE', '%Government%')
                    ->orWhere('subject', 'LIKE', '%Geography%');
            })
            ->with(['modules', 'examBoard'])
            ->limit(9)
            ->latest()
            ->get()
            ->map(function ($course) {
                // Calculate if course is free based on model logic
                $isFree = !$course->is_paid || ($course->price <= 0);

                return [
                    'id' => $course->id,
                    'code' => $course->code,
                    'title' => $course->title,
                    'subject' => $course->subject,
                    'description' => $course->description,
                    'level' => $course->level,
                    'estimated_duration_hours' => $course->estimated_duration_hours,
                    'modules_count' => $course->modules->count(),
                    'status' => $course->status,
                    'slug' => $course->slug,
                    'price' => (float) $course->price,
                    'is_paid' => (bool) $course->is_paid,
                    'is_free' => $isFree,
                    'thumbnail_url' => $course->thumbnail_url,
                    'tags' => $course->tags,
                    'exam_board' => $course->examBoard ? $course->examBoard->name : null,
                    'learning_objectives' => $course->learning_objectives,
                    'has_certificate' => (bool) $course->has_certificate,
                    'current_enrollment' => $course->current_enrollment,
                    // Add calculated fields
                    'display_price' => $isFree ? 'FREE' : '₦' . number_format($course->price, 0),
                    'duration_display' => $course->estimated_duration_hours
                        ? $course->estimated_duration_hours . ' hrs'
                        : 'Self-paced',
                    'enrollment_status' => $course->isFull() ? 'Full' : 'Available',
                ];
            });

        // If no WAEC-specific courses found, get general exam courses
        if ($courses->isEmpty()) {
            $courses = Course::where('visibility', 'public')
                ->where('status', 'active')
                ->where(function($query) {
                    $query->where('subject', 'LIKE', '%Exam%')
                        ->orWhere('title', 'LIKE', '%Exam%')
                        ->orWhere('tags', 'LIKE', '%exam%');
                })
                ->with(['modules', 'examBoard'])
                ->limit(9)
                ->latest()
                ->get()
                ->map(function ($course) {
                    $isFree = !$course->is_paid || ($course->price <= 0);

                    return [
                        'id' => $course->id,
                        'code' => $course->code,
                        'title' => $course->title,
                        'subject' => $course->subject,
                        'description' => $course->description,
                        'level' => $course->level,
                        'estimated_duration_hours' => $course->estimated_duration_hours,
                        'modules_count' => $course->modules->count(),
                        'status' => $course->status,
                        'slug' => $course->slug,
                        'price' => (float) $course->price,
                        'is_paid' => (bool) $course->is_paid,
                        'is_free' => $isFree,
                        'thumbnail_url' => $course->thumbnail_url,
                        'tags' => $course->tags,
                        'exam_board' => $course->examBoard ? $course->examBoard->name : null,
                        'learning_objectives' => $course->learning_objectives,
                        'has_certificate' => (bool) $course->has_certificate,
                        'current_enrollment' => $course->current_enrollment,
                        'display_price' => $isFree ? 'FREE' : '₦' . number_format($course->price, 0),
                        'duration_display' => $course->estimated_duration_hours
                            ? $course->estimated_duration_hours . ' hrs'
                            : 'Self-paced',
                        'enrollment_status' => $course->isFull() ? 'Full' : 'Available',
                    ];
                });
        }

        // Get subscription plans - ensure free plan is included
        $subscriptionPlans = SubscriptionPlan::active()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($plan) {
                $features = is_array($plan->features) ? $plan->features : json_decode($plan->features, true);

                // Add WAEC-specific features for display
                $waecFeatures = [
                    'waec_courses_access' => true,
                    'ai_tutor_access' => true,
                    'practice_tests' => true,
                    'performance_analytics' => $plan->tier === 'premium' || $plan->tier === 'pro',
                    'certificate_included' => $plan->tier !== 'free',
                    'priority_support' => $plan->tier === 'premium' || $plan->tier === 'pro',
                ];

                // Merge with existing features
                $allFeatures = array_merge($features ?? [], $waecFeatures);

                // Format features for display
                $formattedFeatures = array_map(function ($feature) {
                    if (is_bool($feature)) {
                        return $feature ? 'Available' : 'Not available';
                    }
                    return $feature;
                }, $allFeatures);

                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'code' => $plan->code,
                    'description' => $plan->description,
                    'price' => $plan->price,
                    'currency' => $plan->currency,
                    'monthly_price' => $plan->monthly_price,
                    'yearly_price' => $plan->yearly_price,
                    'features' => $formattedFeatures,
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
                    // Add plan-specific data for WAEC page
                    'waec_subjects_limit' => $plan->tier === 'free' ? 2 : 'unlimited',
                    'practice_tests_limit' => $plan->tier === 'free' ? 10 : 'unlimited',
                    'ai_tutor_access_limit' => $plan->tier === 'free' ? 'Limited' : 'Unlimited',
                    'display_price' => $plan->isFree() ? 'FREE' : '₦' . number_format($plan->price, 0) . '/month',
                ];
            });

        // Get WAEC-specific subjects for the subjects section
        $waecSubjects = [
            ['name' => 'Mathematics', 'description' => 'Complete syllabus coverage', 'icon' => '➕'],
            ['name' => 'English Language', 'description' => 'Comprehension & Essay writing', 'icon' => '🔤'],
            ['name' => 'Biology', 'description' => 'Life sciences & experiments', 'icon' => '🧬'],
            ['name' => 'Chemistry', 'description' => 'Organic & inorganic chemistry', 'icon' => '⚗️'],
            ['name' => 'Physics', 'description' => 'Mechanics & modern physics', 'icon' => '⚛️'],
            ['name' => 'Economics', 'description' => 'Micro & macroeconomics', 'icon' => '📈'],
            ['name' => 'Government', 'description' => 'Political science topics', 'icon' => '🏛️'],
            ['name' => 'Geography', 'description' => 'Physical & human geography', 'icon' => '🗺️'],
            ['name' => 'Further Mathematics', 'description' => 'Advanced mathematics topics', 'icon' => '📐'],
            ['name' => 'Literature in English', 'description' => 'Text analysis & comprehension', 'icon' => '📖'],
        ];

        // Get WAEC success statistics
        $waecStats = [
            'student_improvement' => [
                ['subject' => 'Mathematics', 'improvement' => 42, 'color' => '#10b981'],
                ['subject' => 'English', 'improvement' => 38, 'color' => '#0ea5e9'],
                ['subject' => 'Biology', 'improvement' => 45, 'color' => '#8b5cf6'],
                ['subject' => 'Chemistry', 'improvement' => 40, 'color' => '#f59e0b'],
            ],
            'total_students' => 2500,
            'average_score_increase' => 35,
            'overall_pass_rate' => 98,
            'ai_tutor_usage' => [
                'questions_answered' => '10,000+',
                'average_response_time' => '2 minutes',
                'student_satisfaction' => 96,
            ]
        ];

        // Get AI-powered features data
        $aiFeatures = [
            [
                'title' => 'AI Course Creation',
                'description' => 'Every course is created using AI to ensure it follows the latest WAEC syllabus',
                'icon' => 'AI',
                'color' => 'emerald'
            ],
            [
                'title' => 'Dedicated AI Tutor',
                'description' => 'Every WAEC course comes with a dedicated AI tutor for personalized help',
                'icon' => 'T',
                'color' => 'blue'
            ],
            [
                'title' => 'AI-Generated Practice',
                'description' => 'Practice questions generated based on your weak areas',
                'icon' => 'P',
                'color' => 'purple'
            ],
            [
                'title' => 'Performance Analytics',
                'description' => 'AI-powered insights on your progress and areas for improvement',
                'icon' => 'A',
                'color' => 'amber'
            ],
        ];

        return Inertia::render('Frontpages/Landing/Waec', [
            'courses' => $courses,
            'subscriptionPlans' => $subscriptionPlans,
            'waecSubjects' => $waecSubjects,
            'waecStats' => $waecStats,
            'aiFeatures' => $aiFeatures,
            'meta' => [
                'title' => 'WAEC Online Classes in Nigeria | WAEC Exam Preparation Platform – OliLearn',
                'description' => 'Prepare for WAEC in Nigeria with OliLearn. WAEC syllabus-based lessons, AI tutor, quizzes, flashcards, and exam-style practice for SS2 & SS3 students.',
                'image' => asset('images/waec-landing.png'),
                'url' => url()->current(),
                'keywords' => 'WAEC online classes Nigeria, WAEC exam preparation Nigeria, WAEC syllabus lessons, WAEC practice questions, WAEC learning platform',
            ],
            'seo' => [
                'title' => 'WAEC Online Classes in Nigeria | WAEC Exam Preparation Platform – OliLearn',
                'description' => 'Prepare for WAEC in Nigeria with OliLearn. WAEC syllabus-based lessons, AI tutor, quizzes, flashcards, and exam-style practice for SS2 & SS3 students.',
                'canonical' => url()->current(),
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
            ],
            'page_data' => [
                'cta_text' => 'Start Free WAEC Preparation',
                'hero_title' => 'WAEC Online Classes & Exam Preparation for Nigerian Students',
                'hero_subtitle' => 'Pass WAEC with understanding — not guesswork.',
                'total_courses' => $courses->count(),
                'has_free_courses' => $courses->where('is_free', true)->count() > 0,
            ]
        ]);
    }

    public function jamblanding()
    {
        // Get JAMB-specific courses with proper filtering
        $courses = Course::where('visibility', 'public')
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('subject', 'LIKE', '%JAMB%')
                    ->orWhere('title', 'LIKE', '%JAMB%')
                    ->orWhere('tags', 'LIKE', '%JAMB%')
                    ->orWhere('subject', 'LIKE', '%UTME%')
                    ->orWhere('title', 'LIKE', '%UTME%')
                    ->orWhere('tags', 'LIKE', '%UTME%')
                    ->orWhereHas('examBoard', function($q) {
                        $q->where('name', 'LIKE', '%JAMB%');
                    })
                    // Common JAMB subjects
                    ->orWhere('subject', 'LIKE', '%Mathematics%')
                    ->orWhere('subject', 'LIKE', '%English%')
                    ->orWhere('subject', 'LIKE', '%Biology%')
                    ->orWhere('subject', 'LIKE', '%Chemistry%')
                    ->orWhere('subject', 'LIKE', '%Physics%')
                    ->orWhere('subject', 'LIKE', '%Economics%')
                    ->orWhere('subject', 'LIKE', '%Government%')
                    ->orWhere('subject', 'LIKE', '%Geography%')
                    ->orWhere('subject', 'LIKE', '%Commerce%')
                    ->orWhere('subject', 'LIKE', '%Accounting%')
                    ->orWhere('subject', 'LIKE', '%Literature%');
            })
            ->with(['modules', 'examBoard'])
            ->limit(9)
            ->latest()
            ->get()
            ->map(function ($course) {
                // Calculate if course is free based on model logic
                $isFree = !$course->is_paid || ($course->price <= 0);

                return [
                    'id' => $course->id,
                    'code' => $course->code,
                    'title' => $course->title,
                    'subject' => $course->subject,
                    'description' => $course->description,
                    'level' => $course->level,
                    'estimated_duration_hours' => $course->estimated_duration_hours,
                    'modules_count' => $course->modules->count(),
                    'status' => $course->status,
                    'slug' => $course->slug,
                    'price' => (float) $course->price,
                    'is_paid' => (bool) $course->is_paid,
                    'is_free' => $isFree,
                    'thumbnail_url' => $course->thumbnail_url,
                    'tags' => $course->tags,
                    'exam_board' => $course->examBoard ? $course->examBoard->name : null,
                    'learning_objectives' => $course->learning_objectives,
                    'has_certificate' => (bool) $course->has_certificate,
                    'current_enrollment' => $course->current_enrollment,
                    // Add calculated fields
                    'display_price' => $isFree ? 'FREE' : '₦' . number_format($course->price, 0),
                    'duration_display' => $course->estimated_duration_hours
                        ? $course->estimated_duration_hours . ' hrs'
                        : 'Self-paced',
                    'enrollment_status' => $course->isFull() ? 'Full' : 'Available',
                    // JAMB-specific fields
                    //'cbt_practice' => str_contains($course->tags ?? '', 'CBT') || str_contains($course->title ?? '', 'CBT'),
                    //'mock_exams' => str_contains($course->tags ?? '', 'mock') || str_contains($course->title ?? '', 'Mock'),
                ];
            });

        // If no JAMB-specific courses found, get general exam courses with CBT focus
        if ($courses->isEmpty()) {
            $courses = Course::where('visibility', 'public')
                ->where('status', 'active')
                ->where(function($query) {
                    $query->where('subject', 'LIKE', '%Exam%')
                        ->orWhere('title', 'LIKE', '%Exam%')
                        ->orWhere('tags', 'LIKE', '%exam%')
                        ->orWhere('tags', 'LIKE', '%CBT%')
                        ->orWhere('title', 'LIKE', '%CBT%');
                })
                ->with(['modules', 'examBoard'])
                ->limit(9)
                ->latest()
                ->get()
                ->map(function ($course) {
                    $isFree = !$course->is_paid || ($course->price <= 0);

                    return [
                        'id' => $course->id,
                        'code' => $course->code,
                        'title' => $course->title,
                        'subject' => $course->subject,
                        'description' => $course->description,
                        'level' => $course->level,
                        'estimated_duration_hours' => $course->estimated_duration_hours,
                        'modules_count' => $course->modules->count(),
                        'status' => $course->status,
                        'slug' => $course->slug,
                        'price' => (float) $course->price,
                        'is_paid' => (bool) $course->is_paid,
                        'is_free' => $isFree,
                        'thumbnail_url' => $course->thumbnail_url,
                        'tags' => $course->tags,
                        'exam_board' => $course->examBoard ? $course->examBoard->name : null,
                        'learning_objectives' => $course->learning_objectives,
                        'has_certificate' => (bool) $course->has_certificate,
                        'current_enrollment' => $course->current_enrollment,
                        'display_price' => $isFree ? 'FREE' : '₦' . number_format($course->price, 0),
                        'duration_display' => $course->estimated_duration_hours
                            ? $course->estimated_duration_hours . ' hrs'
                            : 'Self-paced',
                        'enrollment_status' => $course->isFull() ? 'Full' : 'Available',
                        //'cbt_practice' => str_contains($course->tags ?? '', 'CBT') || str_contains($course->title ?? '', 'CBT'),
                        //'mock_exams' => str_contains($course->tags ?? '', 'mock') || str_contains($course->title ?? '', 'Mock'),
                    ];
                });
        }

        // Get subscription plans - ensure free plan is included
        $subscriptionPlans = SubscriptionPlan::active()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($plan) {
                $features = is_array($plan->features) ? $plan->features : json_decode($plan->features, true);

                // Add JAMB-specific features for display
                $jambFeatures = [
                    'jamb_cbt_practice' => true,
                    'ai_tutor_access' => true,
                    'timed_mock_exams' => true,
                    'performance_analytics' => $plan->tier === 'premium' || $plan->tier === 'pro',
                    'detailed_explanations' => $plan->tier !== 'free',
                    'unlimited_practice' => $plan->tier === 'premium' || $plan->tier === 'pro',
                    'priority_support' => $plan->tier === 'premium' || $plan->tier === 'pro',
                ];

                // Merge with existing features
                $allFeatures = array_merge($features ?? [], $jambFeatures);

                // Format features for display
                $formattedFeatures = array_map(function ($feature) {
                    if (is_bool($feature)) {
                        return $feature ? 'Available' : 'Not available';
                    }
                    return $feature;
                }, $allFeatures);

                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'code' => $plan->code,
                    'description' => $plan->description,
                    'price' => $plan->price,
                    'currency' => $plan->currency,
                    'monthly_price' => $plan->monthly_price,
                    'yearly_price' => $plan->yearly_price,
                    'features' => $formattedFeatures,
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
                    // Add plan-specific data for JAMB page
                    'jamb_subjects_limit' => $plan->tier === 'free' ? 2 : 'unlimited',
                    'cbt_tests_limit' => $plan->tier === 'free' ? 10 : 'unlimited',
                    'mock_exams_limit' => $plan->tier === 'free' ? 2 : 'unlimited',
                    'ai_tutor_access_limit' => $plan->tier === 'free' ? 'Limited' : 'Unlimited',
                    'display_price' => $plan->isFree() ? 'FREE' : '₦' . number_format($plan->price, 0) . '/month',
                    // JAMB-specific display
                    'jamb_features' => [
                        'cbt_simulator' => $plan->tier !== 'free',
                        'timed_practice' => true,
                        'instant_scoring' => true,
                        'detailed_analytics' => $plan->tier === 'premium' || $plan->tier === 'pro',
                    ]
                ];
            });

        // Get JAMB-specific subjects for the subjects section
        $jambSubjects = [
            ['name' => 'Mathematics', 'description' => 'Complete JAMB syllabus', 'icon' => '➕'],
            ['name' => 'English Language', 'description' => 'Comprehension & Usage', 'icon' => '🔤'],
            ['name' => 'Biology', 'description' => 'Life sciences focus', 'icon' => '🧬'],
            ['name' => 'Chemistry', 'description' => 'Organic & inorganic', 'icon' => '⚗️'],
            ['name' => 'Physics', 'description' => 'Mechanics & waves', 'icon' => '⚛️'],
            ['name' => 'Economics', 'description' => 'Micro & macroeconomics', 'icon' => '📈'],
            ['name' => 'Government', 'description' => 'Political systems', 'icon' => '🏛️'],
            ['name' => 'Geography', 'description' => 'Physical geography', 'icon' => '🗺️'],
            ['name' => 'Commerce', 'description' => 'Business studies', 'icon' => '🏪'],
            ['name' => 'Accounting', 'description' => 'Financial accounting', 'icon' => '📊'],
            ['name' => 'Literature in English', 'description' => 'Text analysis', 'icon' => '📖'],
            ['name' => 'Christian Religious Studies', 'description' => 'CRS topics', 'icon' => '✝️'],
        ];

        // Get JAMB success statistics
        $jambStats = [
            'subject_improvement' => [
                ['subject' => 'Mathematics', 'improvement' => 35, 'color' => '#10b981'],
                ['subject' => 'English Language', 'improvement' => 28, 'color' => '#059669'],
                ['subject' => 'Physics', 'improvement' => 42, 'color' => '#047857'],
                ['subject' => 'Chemistry', 'improvement' => 38, 'color' => '#065f46'],
            ],
            'total_students' => 3000,
            'average_improvement' => 25,
            'cbt_speed_improvement' => 40,
            'accuracy_increase' => 35,
            'ai_tutor_usage' => [
                'questions_answered' => '15,000+',
                'average_response_time' => '1 minute',
                'student_satisfaction' => 95,
            ]
        ];

        // Get JAMB-specific features data
        $jambFeatures = [
            [
                'title' => 'Real JAMB-style CBT',
                'description' => 'Simulated exam environment with timed practice just like the actual JAMB',
                'icon' => '💻',
                'color' => 'emerald'
            ],
            [
                'title' => 'Instant Scoring & Feedback',
                'description' => 'Get results immediately with detailed explanations for every question',
                'icon' => '⚡',
                'color' => 'blue'
            ],
            [
                'title' => 'AI-Powered Tutoring',
                'description' => 'Step-by-step explanations for difficult questions when you need help',
                'icon' => '🤖',
                'color' => 'purple'
            ],
            [
                'title' => 'Performance Analytics',
                'description' => 'Track progress, identify weak areas, and focus your study time effectively',
                'icon' => '📊',
                'color' => 'amber'
            ],
            [
                'title' => 'JAMB Syllabus Coverage',
                'description' => 'Complete coverage of all JAMB subjects and topics for UTME',
                'icon' => '📚',
                'color' => 'green'
            ],
            [
                'title' => 'Mobile-Friendly Practice',
                'description' => 'Study on any device, practice CBT questions anytime, anywhere',
                'icon' => '📱',
                'color' => 'indigo'
            ],
        ];

        return Inertia::render('Frontpages/Landing/Jamb', [
            'courses' => $courses,
            'subscriptionPlans' => $subscriptionPlans,
            'jambSubjects' => $jambSubjects,
            'jambStats' => $jambStats,
            'jambFeatures' => $jambFeatures,
            'meta' => [
                'title' => 'JAMB CBT Practice Online in Nigeria | OliLearn',
                'description' => 'Prepare for JAMB with CBT practice, AI tutoring, and JAMB-aligned lessons. Built for Nigerian students. Start free on OliLearn.',
                'image' => asset('images/jamb-landing.png'),
                'url' => url()->current(),
                'keywords' => 'JAMB CBT practice Nigeria, JAMB online preparation, UTME exam practice, JAMB mock exams, JAMB AI tutor, Nigerian university entrance exam',
            ],
            'seo' => [
                'title' => 'JAMB CBT Practice Online in Nigeria | OliLearn',
                'description' => 'Prepare for JAMB with CBT practice, AI tutoring, and JAMB-aligned lessons. Built for Nigerian students. Start free on OliLearn.',
                'canonical' => url()->current(),
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
            ],
            'page_data' => [
                'cta_text' => 'Start Free JAMB CBT Practice',
                'hero_title' => 'Score Higher in JAMB — With Smart CBT Practice & AI Guidance',
                'hero_subtitle' => 'Prepare for JAMB the right way: understand concepts, practice real CBT questions, and master time management before exam day.',
                'total_courses' => $courses->count(),
                'has_free_courses' => $courses->where('is_free', true)->count() > 0,
                'cbt_courses_count' => $courses->where('cbt_practice', true)->count(),
            ]
        ]);
    }

}
