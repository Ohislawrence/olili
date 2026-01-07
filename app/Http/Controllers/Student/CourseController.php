<?php
// app/Http/Controllers/Student/CourseController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CapstoneProject;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Module;
use App\Models\CourseOutline;
use App\Models\CourseContent;
use App\Models\ExamBoard;
use App\Models\StudentTopicProgress; // Add this
use App\Services\CertificateGenerationService;
use App\Services\ContentGenerationService;
use App\Services\CourseNotificationService;
use App\Services\ProgressTrackingService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;

class CourseController extends Controller
{
    protected $contentGenerationService;
    protected $progressService;

    public function __construct(
        ContentGenerationService $contentGenerationService,
        ProgressTrackingService $progressService
    ) {
        $this->contentGenerationService = $contentGenerationService;
        $this->progressService = $progressService;
    }

    public function index(Request $request)
    {
        $student = auth()->user();

        // Get courses through enrollments with proper eager loading
        // EXCLUDE dropped courses by default
        $enrollmentsQuery = $student->courseEnrollments()
            ->where('status', '!=', 'dropped') // Filter out dropped courses
            ->with([
                'course.examBoard',
                'course.modules.topics'
            ]);

        // Filter by enrollment status
        if ($request->has('status') && $request->status) {
            $enrollmentsQuery->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $enrollmentsQuery->whereHas('course', function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                ->orWhere('subject', 'like', "%{$request->search}%");
            });
        }

        $enrollments = $enrollmentsQuery->latest()->paginate(12);

        // Transform enrollments to include course data and progress
        $coursesData = $enrollments->getCollection()->map(function ($enrollment) {
            $course = $enrollment->course;

            // Use the ProgressTrackingService to calculate progress
            $progress = $this->progressService->calculateCourseProgress($course, $enrollment->user_id);
            $lastViewedTopic = $this->progressService->lastViewedTopic($enrollment);

            return [
                'id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'title' => $course->title,
                'subject' => $course->subject,
                'description' => $course->description,
                'level' => $course->level,
                'status' => $enrollment->status,
                'progress_percentage' => $progress['overall_completion_percentage'],
                'enrolled_at' => $enrollment->enrolled_at,
                'started_at' => $enrollment->started_at,
                'completed_at' => $enrollment->completed_at,
                'exam_board' => $course->examBoard,
                'modules_count' => $progress['total_modules'],
                'completed_modules_count' => $progress['completed_modules'],
                'topics_count' => $progress['total_topics'],
                'completed_topics_count' => $progress['completed_topics'],
                'overall_completion_percentage' => $progress['overall_completion_percentage'],
                'enrollment' => $enrollment,
                'total_time_minutes' => $progress['total_time_minutes'],
                'average_score' => $progress['average_score'],
                'lastViewedTopic' => $lastViewedTopic,
            ];
        });

        return Inertia::render('Student/Courses/Index', [
            'courses' => new LengthAwarePaginator(
                $coursesData,
                $enrollments->total(),
                $enrollments->perPage(),
                $enrollments->currentPage(),
                ['path' => request()->url()]
            ),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function browse(Request $request)
    {
        // Show available courses for enrollment
        $query = Course::availableForEnrollment()
            ->with(['examBoard', 'creator'])
            ->withCount('enrollments');

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('subject', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Filter by subject
        if ($request->has('subject') && $request->subject) {
            $query->where('subject', $request->subject);
        }

        // Filter by level
        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        // Filter by exam board
        if ($request->has('exam_board_id') && $request->exam_board_id) {
            $query->where('exam_board_id', $request->exam_board_id);
        }

        $courses = $query->latest()->paginate(12);

        // Get current student's enrollments (excluding dropped)
        $student = auth()->user();
        $enrolledCourseIds = $student->courseEnrollments()
            ->where('status', '!=', 'dropped') // Exclude dropped courses
            ->pluck('course_id')
            ->toArray();

        // Get enrolled courses with progress for enrolled students
        $enrolledCourses = [];
        foreach ($student->courseEnrollments as $enrollment) {
            // Skip dropped enrollments
            if ($enrollment->status === 'dropped') {
                continue;
            }

            $lastViewedTopic = $this->progressService->lastViewedTopic($enrollment);

            // Use ProgressTrackingService to get enrollment progress
            $progress = $this->progressService->getEnrollmentProgress($enrollment);

            $enrolledCourses[$enrollment->course_id] = [
                'id' => $enrollment->id,
                'progress_percentage' => $progress['overall_completion_percentage'],
                'status' => $enrollment->status,
                'lastTopic' => $lastViewedTopic,
            ];
        }

        // Get available subjects for filter
        $subjects = Course::distinct()->orderBy('subject')->pluck('subject');

        $examBoards = ExamBoard::active()->get();

        $levels = [
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
        ];

        return Inertia::render('Student/Catalog/Browse', [
            'courses' => $courses,
            'enrolled_course_ids' => $enrolledCourseIds,
            'enrolled_courses' => $enrolledCourses,
            'subjects' => $subjects,
            'levels' => $levels,
            'exam_boards' => $examBoards,
            'total_courses' => $courses->total(),
            'filters' => $request->only(['search', 'subject', 'level', 'exam_board_id']),
        ]);
    }

    public function preview(Course $course)
    {
        $student = auth()->user();

        // Check if student is enrolled
        $enrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->first();

        // If enrolled, redirect to course show page


        // Load course with required relationships
        $course->load([
            'examBoard',
            'creator',
            'modules' => function ($query) {
                $query->orderBy('order')
                    ->with(['topics' => function ($q) {
                        $q->orderBy('order');
                    }]);
            },
        ]);

        // Calculate module and topic counts
        $moduleCount = $course->modules->count();
        $topicCount = $course->modules->sum(function ($module) {
            return $module->topics->count();
        });

        // Enrollment statistics
        $enrollmentStats = [
            'total' => $course->current_enrollment,
            'limit' => $course->enrollment_limit,
            'available' => $course->enrollment_limit
                ? max(0, $course->enrollment_limit - $course->current_enrollment)
                : null,
        ];

        return Inertia::render('Student/Catalog/Show', [
            'course' => $course,
            'is_enrolled' => false,
            'enrolled_course' => null,
            'can_enroll' => $course->canEnroll($student),
            'module_count' => $moduleCount,
            'topic_count' => $topicCount,
            'enrollment_stats' => $enrollmentStats,
        ]);
    }

    public function show(Course $course)
    {
        $student = auth()->user();

        // Check if student is enrolled (excluding dropped enrollments)
        $enrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->where('status', '!=', 'dropped')
            ->first();

        $lastViewedTopic = $this->progressService->lastViewedTopic($enrollment);

        // If student has dropped this course, we should allow them to see it
        $droppedEnrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->where('status', 'dropped')
            ->first();

        $isEnrolled = $enrollment !== null;
        $wasDropped = $droppedEnrollment !== null;

        // Load basic course info for both enrolled and non-enrolled students
        $course->load(['examBoard', 'creator', 'modules.topics']);

        // Get course progress for both enrolled and non-enrolled students
        // For non-enrolled, we'll show empty stats or basic course info
        if ($isEnrolled) {
            $courseProgress = $this->progressService->calculateCourseProgress($course, $student->id);
            $nextTopic = $this->getNextTopic($course, $enrollment);

            // Load additional details for enrolled students
            $course->load([
                'modules' => function ($query) {
                    $query->orderBy('order')
                        ->with(['topics' => function ($query) {
                            $query->orderBy('order');
                        }]);
                },
                'modules.topics.contents',
                'modules.topics.quiz',
                'capstoneProject',
            ]);
        } else {
            // Create empty course stats for non-enrolled students
            $courseProgress = [
                'completed_modules' => 0,
                'total_modules' => $course->modules->count(),
                'completed_topics' => 0,
                'total_topics' => $course->modules->sum(fn($module) => $module->topics->count()),
                'module_completion_percentage' => 0,
                'topic_completion_percentage' => 0,
                'overall_completion_percentage' => 0,
                'total_time_minutes' => 0,
                'completed_time_minutes' => 0,
                'estimated_total_minutes' => 0,
                'average_score' => 0,
                'estimated_remaining_time' => 0,
                'progress_by_module' => [],
            ];
            $nextTopic = null;
        }

        // Check if the student can enroll (considering course capacity, etc.)
        $canEnroll = $course->canEnroll($student);

        return Inertia::render('Student/Courses/Show', [
            'course' => $course,
            'can_enroll' => $canEnroll,
            'is_enrolled' => $isEnrolled,
            'was_dropped' => $wasDropped,
            'lastViewedTopic' => $lastViewedTopic,
            'dropped_enrollment' => $wasDropped ? [
                'id' => $droppedEnrollment->id,
                'dropped_at' => $droppedEnrollment->dropped_at,
                'progress_percentage' => $droppedEnrollment->progress_percentage,
            ] : null,
            'next_topic' => $nextTopic ? [
                'id' => $nextTopic->id,
                'title' => $nextTopic->title,
                'module_title' => $nextTopic->module->title,
                'estimated_duration_minutes' => $nextTopic->estimated_duration_minutes,
                'module_id' => $nextTopic->module_id,
            ] : null,
            'course_stats' => $courseProgress, // Always pass valid course stats
            'enrollment' => $isEnrolled ? [
                'id' => $enrollment->id,
                'status' => $enrollment->status,
                'progress_percentage' => $enrollment->progress_percentage,
                'enrolled_at' => $enrollment->enrolled_at,
                'started_at' => $enrollment->started_at,
                'completed_at' => $enrollment->completed_at,
            ] : null,
        ]);
    }

    public function enroll(Request $request, Course $course)
    {
        $student = auth()->user();

        // Check if already enrolled (excluding dropped)
        $existingEnrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->where('status', '!=', 'dropped')
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('student.courses.learn', $course->id)
                ->with('info', 'You are already enrolled in this course.');
        }

        // Check if student previously dropped this course
        $droppedEnrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->where('status', 'dropped')
            ->first();

        if ($droppedEnrollment) {
            // Instead of creating a new enrollment, reactivate the dropped one
            $droppedEnrollment->update([
                'status' => 'enrolled',
                'dropped_at' => null,
                //'progress_percentage' => 0, // Reset progress or keep previous?
                // You can decide whether to keep previous progress
            ]);

            return redirect()->route('student.courses.learn', $course->id)
                ->with('success', 'Successfully re-enrolled in the course! Your previous progress has been restored.');
        }

        // Enroll in course (new enrollment)
        $enrollment = $course->enrollStudent($student);

        if (!$enrollment) {
            return redirect()->back()
                ->with('error', 'Unable to enroll in this course. Please check if the course is available and has space.');
        }

        return redirect()->route('student.courses.learn', $course->id)
            ->with('success', 'Successfully enrolled in the course!');
    }

    public function startCourse(Request $request, Course $course)
    {
        $student = auth()->user();

        $enrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->whereIn('status', ['enrolled', 'active'])
            ->first();

        if (! $enrollment) {
            return redirect()
                ->back()
                ->with('error', 'You are not enrolled in this course.');
        }

        if ($enrollment->status !== 'active') {
            $enrollment->update(['status' => 'active',
                                'started_at' => now(),
                                ]);
        }

        return redirect()
            ->route('student.courses.learn', $course->id)
            ->with('success', 'You have started this course!');
    }

    public function learn(Course $course, Request $request)
    {
        $student = auth()->user();

        // Get the requested tab from query parameters
        $activeTab = $request->get('tab', 'content');

        $course->load([
            'chatSessions',
            'progressTracking',
        ]);

        $currentTopicId = $request->get('topic');
        $currentTopic = null;

        $enrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->where('status', '!=', 'dropped')
            ->first();


        if(!$currentTopicId) {
            $currentTopicId = CourseOutline::whereHas('module', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })->firstOrFail()->id;

            $currentTopicId;
        }

        if ($currentTopicId) {
            $currentTopic = CourseOutline::whereHas('module', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })
                ->where('id', $currentTopicId)
                ->with([
                    'contents',
                    'quiz' => function($query) use ($student) {
                        $query->with(['attempts' => function($query) use ($student) {
                            $query->where('user_id', $student->id)
                                ->orderBy('created_at', 'desc');
                        }]);
                    },
                    'module'
                ])
                ->firstOrFail();
        } else {
            $currentTopic = $this->getNextTopic($course, $enrollment);

            if (!$currentTopic) {
                $course->update([
                    'status' => 'completed',
                    'actual_completion_date' => now(),
                ]);

                return redirect()->route('student.courses.show', $course->id)
                    ->with('success', 'Congratulations! You have completed this course.');
            }
        }

        // Get course structure for navigation
        $courseStructure = $course->modules()
            ->with(['topics' => function ($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get()
            ->map(function ($module) {
                return [
                    'id' => $module->id,
                    'title' => $module->title,
                    'description' => $module->description,
                    'order' => $module->order,
                    'is_completed' => $module->is_completed,
                    'estimated_duration_minutes' => $module->estimated_duration_minutes,
                    'topics' => $module->topics->map(function ($topic) {
                        return [
                            'id' => $topic->id,
                            'title' => $topic->title,
                            'type' => $topic->type,
                            'order' => $topic->order,
                            'is_completed' => $topic->is_completed,
                            'has_quiz' => $topic->has_quiz,
                            'has_project' => $topic->has_project,
                            'estimated_duration_minutes' => $topic->estimated_duration_minutes,
                        ];
                    }),
                ];
            });

        $courseStats = $this->progressService->calculateCourseProgress($course);
        $isTopicComplete = $this->progressService->isTopicCompletedForEnrollment($enrollment,$currentTopicId);

        // Check if we need to show a specific tab
        $showQuizTab = false;
        if ($request->has('tab') && $request->tab === 'quiz') {
            $showQuizTab = true;
        }

        return Inertia::render('Student/Courses/Learn', [
            'course' => $course->load(['capstoneProject','modules']),
            'current_topic' => $currentTopic->load('contents'),
            'course_structure' => $courseStructure,
            'current_module' => $currentTopic->module,
            'course_stats' => $courseStats,
            'active_tab' => $activeTab,
            'show_quiz_tab' => $showQuizTab,
            'isTopicComplete' => $isTopicComplete,
        ]);
    }

    // Helper method to get previous topic
    private function getPreviousTopic(CourseOutline $currentTopic)
    {
        return CourseOutline::where('module_id', $currentTopic->module_id)
            ->where('order', '<', $currentTopic->order)
            ->orderBy('order', 'desc')
            ->first();
    }

    public function completeTopic(CourseOutline $topic, Request $request)
    {
        $student = auth()->user();
        $course = $topic->module->course;

        // Get enrollment
        $enrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Record completion activity using ProgressTrackingService
        $this->progressService->recordActivity(
            $student,
            $course,
            $enrollment->id,
            $topic->id,
            'outline_completed',
            $request->time_spent ?? 0,
            true,
            $request->score ?? null
        );

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Topic completed successfully!',
                'topic' => [
                    'id' => $topic->id,
                    'is_completed' => true
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Topic completed successfully!');
    }

    public function generateContent(CourseOutline $outline)
    {
        try {
            //$content = $this->contentGenerationService->generateContentForOutline($outline, 'text');
            return redirect()->back()->with('success', 'Content generated!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to generate content: ' . $e->getMessage());
        }
    }

    public function generateQuiz(Request $request, $courseId, $outlineId)
    {
        // Find the models manually
        $course = Course::findOrFail($courseId);
        $outline = CourseOutline::findOrFail($outlineId);

        // Verify the outline belongs to the course
        if ($outline->module->course_id !== $course->id) {
            return redirect()->back()
                ->with('error', 'Topic does not belong to this course.');
        }

        try {
            $quiz = $this->contentGenerationService->generateQuizForOutline($outline, 5);

            // Redirect back to the learn page with quiz tab active
            return redirect()->route('student.courses.learn', [
                'course' => $course->id,
                'topic' => $outline->id,
                'tab' => 'quiz'
            ])->with('success', 'Quiz generated successfully!')
              ->with('quiz_generated', true);

        } catch (\Exception $e) {
            \Log::error('Quiz generation failed', [
                'outline_id' => $outline->id,
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to generate quiz: ' . $e->getMessage());
        }
    }

    public function updateProgress(Course $course, Request $request)
    {
        $student = auth()->user();

        // Get enrollment
        $enrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->firstOrFail();

        $this->progressService->recordActivity(
            $student,
            $course,
            $enrollment->id,
            $request->topic_id,
            $request->activity_type ?? 'content_view',
            $request->time_spent ?? 0,
            $request->completed ?? false,
            $request->score ?? null
        );

        // Check and send notifications if needed
        $notificationService = app(CourseNotificationService::class);
        $notificationService->checkAndSendDueSoonNotifications();

        return response()->json(['success' => true]);
    }

    public function pauseCourse(Course $course)
    {
        $student = auth()->user();

        $enrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->firstOrFail();

        $enrollment->pause();

        return redirect()->back()->with('success', 'Course paused. You can resume anytime.');
    }

    public function resumeCourse(Course $course)
    {
        $student = auth()->user();

        $enrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->firstOrFail();

        $enrollment->resume();

        return redirect()->back()->with('success', 'Course resumed! Welcome back.');
    }

    public function dropCourse(Course $course)
    {
        $student = auth()->user();

        $enrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->where('status', '!=', 'dropped') // Don't allow dropping already dropped courses
            ->firstOrFail();

        $enrollment->drop();

        // Redirect to browse page instead of index since dropped courses won't show in index
        return redirect()->route('student.catalog.browse')
            ->with('success', 'You have dropped the course. You can enroll again anytime.');
    }

    /**
     * Get the next topic that should be studied
     */
    private function getNextTopic(Course $course, CourseEnrollment $enrollment, CourseOutline $currentTopic = null): ?CourseOutline
    {
        // Get all modules with their topics
        $modules = $course->modules()
            ->with(['topics' => function ($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        $startLooking = $currentTopic === null;

        foreach ($modules as $module) {
            foreach ($module->topics as $topic) {
                if ($currentTopic && $topic->id === $currentTopic->id) {
                    $startLooking = true;
                    continue;
                }

                if ($startLooking) {
                    // Check if topic is completed for this user using ProgressTrackingService
                    $isCompleted = $this->progressService->isTopicCompletedForEnrollment($enrollment, $topic->id);
                    if (!$isCompleted) {
                        return $topic;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Get module details with progress
     */
    public function showModule(Course $course, Module $module)
    {
        $student = auth()->user();

        $enrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->firstOrFail();

        $module->load(['topics' => function ($query) {
            $query->orderBy('order');
        }, 'topics.contents']);

        // Use ProgressTrackingService to get module progress
        $moduleProgress = $this->progressService->getModuleProgressDetail($module, $student);

        return Inertia::render('Student/Courses/Module', [
            'course' => $course,
            'enrollment' => $enrollment,
            'module' => $module,
            'module_progress' => $moduleProgress,
            'next_topic' => $this->getNextTopicInModule($module, $enrollment),
        ]);
    }

    /**
     * Get the next topic within a specific module
     */
    private function getNextTopicInModule(Module $module, CourseEnrollment $enrollment): ?CourseOutline
    {
        foreach ($module->topics()->orderBy('order')->get() as $topic) {
            // Check if topic is completed for this enrollment using ProgressTrackingService
            $isCompleted = $this->progressService->isTopicCompletedForEnrollment($enrollment, $topic->id);
            if (!$isCompleted) {
                return $topic;
            }
        }

        return null;
    }

    /**
     * Complete a module (mark all topics as completed)
     */
    public function completeModule(Module $module, Request $request)
    {
        $student = auth()->user();
        $enrollment = $student->courseEnrollments()
            ->where('course_id', $module->course_id)
            ->firstOrFail();

        // Mark all topics in the module as completed using ProgressTrackingService
        foreach ($module->topics as $topic) {
            $isCompleted = $this->progressService->isTopicCompletedForEnrollment($enrollment, $topic->id);
            if (!$isCompleted) {
                $this->progressService->recordActivity(
                    $student,
                    $module->course,
                    $enrollment->id,
                    $topic->id,
                    'module_completion',
                    $request->time_spent ?? 0,
                    true,
                    100
                );
            }
        }

        return redirect()->route('student.courses.show', $module->course_id)
            ->with('success', "Module '{$module->title}' completed successfully!");
    }

    public function submit(CapstoneProject $capstoneProject, Request $request)
    {
        $student = auth()->user();
        $enrollment = $student->courseEnrollments()
            ->where('course_id', $capstoneProject->course_id)
            ->firstOrFail();

        // Check if course is completed or near completion
        // Use ProgressTrackingService to get accurate progress
        $courseProgress = $this->progressService->calculateCourseProgress(
            $capstoneProject->course,
            $student->id
        );

        if ($courseProgress['overall_completion_percentage'] < 80) {
            return redirect()->route('student.courses.show', $capstoneProject->course_id)
                ->with('error', 'You need to complete at least 80% of the course before starting the capstone project.');
        }

        $request->validate([
            'submission' => 'required|string|min:100',
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240', // 10MB max
        ]);

        $filePaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('capstone-submissions/' . $capstoneProject->id, 'public');
                $filePaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
        }

        $capstoneProject->submitProject($request->submission, $filePaths);

        return redirect()->route('student.capstone-projects.show', $capstoneProject->id)
            ->with('success', 'Project submitted successfully! It will be graded soon.');
    }

    private function getPopularSubjects()
    {
        return [
            'Mathematics',
            'Physics',
            'Chemistry',
            'Biology',
            'English Language',
            'Literature',
            'History',
            'Geography',
            'Economics',
            'Accounting',
            'Business Studies',
            'Computer Science',
            'Programming',
            'Data Science',
            'Artificial Intelligence',
            'Web Development',
            'Mobile Development',
            'Graphic Design',
            'Music',
            'Art',
        ];
    }

    private function getStudentLevels()
    {
        return [
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
            'expert' => 'Expert',
        ];
    }

    protected function markCourseAsCompleted(Course $course)
    {
        $student = auth()->user();
        $enrollment = $student->courseEnrollments()
            ->where('course_id', $course->id)
            ->firstOrFail();

        $enrollment->complete();

        // Generate certificate if eligible
        if (app(CertificateGenerationService::class)->isEligibleForCertificate($student, $course)) {
            try {
                $organization = $student->organizationProfile;
                app(CertificateGenerationService::class)->generateCertificate(
                    $student,
                    $course,
                    $organization
                );
            } catch (\Exception $e) {
                \Log::error('Failed to generate certificate: ' . $e->getMessage());
            }
        }
    }
}
