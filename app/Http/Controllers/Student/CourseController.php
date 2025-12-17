<?php
// app/Http/Controllers/Student/CourseController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CapstoneProject;
use App\Models\Course;
use App\Models\Module;
use App\Models\CourseOutline;
use App\Models\CourseContent;
use App\Models\ExamBoard;
use App\Services\CourseGenerationService;
use App\Services\ContentGenerationService;
use App\Services\CourseNotificationService;
use App\Services\ProgressTrackingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    protected $courseGenerationService;
    protected $contentGenerationService;
    protected $progressService;

    public function __construct(
        CourseGenerationService $courseGenerationService,
        ContentGenerationService $contentGenerationService,
        ProgressTrackingService $progressService
    ) {
        $this->courseGenerationService = $courseGenerationService;
        $this->contentGenerationService = $contentGenerationService;
        $this->progressService = $progressService;
    }

    public function index(Request $request)
    {
        $student = auth()->user();
        $studentProfile = $student->studentProfile;

        $query = $student->courses()
            ->with([
                'examBoard',
                'modules' => function ($query) {
                    $query->with(['topics' => function ($query) {
                        $query->orderBy('order');
                    }])->orderBy('order');
                }
            ]);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                ->orWhere('subject', 'like', "%{$request->search}%");
            });
        }

        $courses = $query->latest()->paginate(12);

        // Use ProgressTrackingService for accurate progress calculations
        $courses->getCollection()->transform(function ($course) {
            $progress = app(ProgressTrackingService::class)->calculateCourseProgress($course);

            // Add progress data to the course object
            $course->progress_percentage = $progress['overall_completion_percentage'];
            $course->modules_count = $progress['total_modules'];
            $course->completed_modules_count = $progress['completed_modules'];
            $course->topics_count = $progress['total_topics'];
            $course->completed_topics_count = $progress['completed_topics'];
            $course->overall_completion_percentage = $progress['overall_completion_percentage'];
            $course->total_time_minutes = $progress['total_time_minutes'];

            return $course;
        });

        return Inertia::render('Student/Courses/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'status']),
            'student_profile' => $studentProfile,
        ]);
    }

    public function create()
    {
        $student = auth()->user();
        $studentProfile = $student->studentProfile;

        $examBoards = ExamBoard::active()->get();

        return Inertia::render('Student/Courses/Create', [
            'student_profile' => $studentProfile,
            'exam_boards' => $examBoards,
            'subjects' => $this->getPopularSubjects(),
            'student_levels' => $this->getStudentLevels(),
        ]);
    }

    public function store(Request $request)
    {
        $student = auth()->user();
        $studentProfile = $student->studentProfile;

        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_board_id' => 'nullable|exists:exam_boards,id',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'string|max:500',
            'target_level' => 'required|string|in:beginner,intermediate,advanced,expert',
            'target_completion_date' => 'required|date|after:today',
            'weekly_study_hours' => 'required|integer|min:1|max:40',
        ]);

        try {
            // Update student profile if needed
            if ($request->target_level !== $studentProfile->target_level ||
                $request->weekly_study_hours !== $studentProfile->weekly_study_hours) {
                $studentProfile->update([
                    'target_level' => $request->target_level,
                    'weekly_study_hours' => $request->weekly_study_hours,
                    'target_completion_date' => $request->target_completion_date,
                ]);
            }

            $course = $this->courseGenerationService->generateCourse($studentProfile, [
                'title' => $request->title,
                'subject' => $request->subject,
                'description' => $request->description,
                'exam_board_id' => $request->exam_board_id,
                'learning_objectives' => $request->learning_objectives,
            ]);

            // Return JSON response for AJAX handling
            return response()->json([
                'success' => true,
                'redirect' => route('student.courses.show', $course->id),
                'message' => 'Course created successfully! Your personalized learning path has been generated.',
                'course_id' => $course->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Course creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create course: ' . $e->getMessage(),
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function show(Course $course)
    {
        // Ensure the course belongs to the current student
        //$this->authorize('view', $course);

        $course->load([
            'chatSessions',
            'progressTracking',
            'examBoard',
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

        //$creator = $course->creator->id === auth()->user()->id ? true : false;


        $nextTopic = $this->getNextTopic($course);
        $courseStats = $this->progressService->calculateCourseProgress($course);
        //dd($courseStats);
        return Inertia::render('Student/Courses/Show', [
            'course' => $course,
            'next_topic' => $nextTopic,
            'course_stats' => $courseStats,
        ]);
    }

    public function startCourse(Course $course)
    {
        //$this->authorize('update', $course);

        $course->update([
            'status' => 'active',
            'start_date' => now(),
        ]);

        return redirect()->route('student.courses.learn', $course->id)
            ->with('success', 'Course started! Begin with the first topic.');
    }

    public function learn(Course $course, Request $request)
    {
        $student = auth()->user();
        //$this->authorize('view', $course);
        //dd($course->chatSessions);
        $course->load([
            'chatSessions',
            'progressTracking',
        ]);

        $currentTopicId = $request->get('topic');
        $currentTopic = null;

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
            $currentTopic = $this->getNextTopic($course);

            if (!$currentTopic) {
                // All topics completed
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

        return Inertia::render('Student/Courses/Learn', [
            'course' => $course->load(['capstoneProject','modules']),
            'current_topic' => $currentTopic->load('contents'),
            'course_structure' => $courseStructure,
            'current_module' => $currentTopic->module,
            'course_stats' => $courseStats,
        ]);
    }

    public function completeTopic(CourseOutline $topic, Request $request)
    {
        //$this->authorize('update', $topic->module->course);
        $outline = $topic;
        $student = auth()->user();
        $course = $outline->module->course;

        // Record completion activity
        $this->progressService->recordActivity(
            $student,
            $course,
            $outline->id,
            'content_view',
            $request->time_spent ?? 0,
            true,
            $request->score ?? null
        );

        // Mark topic as completed
        $outline->markAsCompleted();

        // Update module completion status
        $this->progressService->updateModuleCompletion($outline->module);

        return redirect()->back()->with('success', 'Completed!');
    }

    public function generateContent(CourseOutline $outline)
    {
        try {
            $content = $this->contentGenerationService->generateContentForOutline($outline, 'text');
            return redirect()->back()->with('success', 'Happy learning!');

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

        //$this->authorize('update', $course);

        // Verify the outline belongs to the course
        if ($outline->module->course_id !== $course->id) {
            return redirect()->back()
                ->with('error', 'Topic does not belong to this course.');
        }

        try {
            $quiz = $this->contentGenerationService->generateQuizForOutline($outline, 5);

            return redirect()->back()->with('success', 'Quiz generated successfully!');

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
        //$this->authorize('update', $course);

        $student = auth()->user();

        $this->progressService->recordActivity(
            $student,
            $course,
            $request->topic_id,
            $request->activity_type ?? 'content_view',
            $request->time_spent ?? 0,
            $request->completed ?? false,
            $request->score ?? null
        );

        // Check and send notifications if needed
        $notificationService = app(CourseNotificationService::class);
        $notificationService->checkAndSendDueSoonNotifications();

        //return redirect()->back()->with('success', 'Progress updated!');
        return response()->json(['success' => true]);
    }

    public function pauseCourse(Course $course)
    {
        //$this->authorize('update', $course);

        $course->update(['status' => 'paused']);

        return redirect()->back()->with('success', 'Course paused. You can resume anytime.');
    }

    public function resumeCourse(Course $course)
    {
        //$this->authorize('update', $course);

        $course->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Course resumed! Welcome back.');
    }

    /**
     * Get the next topic that should be studied
     */
    private function getNextTopic(Course $course): ?CourseOutline
    {
        // Get all modules with their topics
        $modules = $course->modules()
            ->with(['topics' => function ($query) {
                $query->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        foreach ($modules as $module) {
            foreach ($module->topics as $topic) {
                if (!$topic->is_completed) {
                    return $topic;
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
        //$this->authorize('view', $course);

        $module->load(['topics' => function ($query) {
            $query->orderBy('order');
        }, 'topics.contents']);

        $moduleProgress = $this->progressService->getModuleProgressDetail($module, auth()->user());

        return Inertia::render('Student/Courses/Module', [
            'course' => $course,
            'module' => $module,
            'module_progress' => $moduleProgress,
            'next_topic' => $this->getNextTopicInModule($module),
        ]);
    }

    /**
     * Get the next topic within a specific module
     */
    private function getNextTopicInModule(Module $module): ?CourseOutline
    {
        return $module->topics()
            ->where('is_completed', false)
            ->orderBy('order')
            ->first();
    }

    /**
     * Complete a module (mark all topics as completed)
     */
    public function completeModule(Module $module, Request $request)
    {
        //$this->authorize('update', $module->course);

        $student = auth()->user();

        // Mark all topics in the module as completed
        foreach ($module->topics as $topic) {
            if (!$topic->is_completed) {
                $this->progressService->recordActivity(
                    $student,
                    $module->course,
                    $topic->id,
                    'module_completion',
                    0, // No additional time spent
                    true,
                    100 // Perfect score for completion
                );
                $topic->markAsCompleted();
            }
        }

        // Mark module as completed
        $module->markAsCompleted();

        return redirect()->route('student.courses.show', $module->course_id)
            ->with('success', "Module '{$module->title}' completed successfully!");
    }

    public function submit(CapstoneProject $capstoneProject, Request $request)
    {
        //$this->authorize('update', $capstoneProject);

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


}
