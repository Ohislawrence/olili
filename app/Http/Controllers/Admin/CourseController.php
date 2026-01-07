<?php
// app/Http/Controllers/Admin/CourseController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ExamBoard;
use App\Services\CourseGenerationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Services\ContentGenerationService;
use Carbon\Carbon;
use App\Jobs\GenerateTopicQuizJob;
use App\Jobs\GenerateTopicContentJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use App\Models\Module;
use App\Models\CourseOutline;
use App\Jobs\GenerateCourseContentJob;
use App\Jobs\GenerateCourseQuizzesJob;
use App\Jobs\GenerateCapstoneProjectJob;
use Illuminate\Support\Collection;

class CourseController extends Controller
{
    protected $courseGenerationService;
    protected $contentGenerationService;

    public function __construct(CourseGenerationService $courseGenerationService, ContentGenerationService $contentGenerationService)
    {
        $this->courseGenerationService = $courseGenerationService;
        $this->contentGenerationService = $contentGenerationService;
    }

    public function index(Request $request)
    {
        $query = Course::with(['examBoard', 'creator'])
            ->withCount(['modules', 'enrollments', 'activeEnrollments', 'completedEnrollments'])
            ;

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('subject', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by subject
        if ($request->has('subject') && $request->subject) {
            $query->where('subject', $request->subject);
        }

        // Filter by level
        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        // Filter by visibility
        if ($request->has('visibility') && $request->visibility) {
            if ($request->visibility === 'public') {
                $query->where('is_public', true)->where('visibility', 'public');
            } elseif ($request->visibility === 'private') {
                $query->where('is_public', false);
            }
        }

        $courses = $query->latest()->paginate(20);

        // Calculate stats for admin-created courses only
        $stats = [
            'total_courses' => Course::where('created_by', 'admin')->count(),
            'active_courses' => Course::where('created_by', 'admin')->where('status', 'active')->count(),
            'published_courses' => Course::where('created_by', 'admin')
                ->where('is_public', true)
                ->where('visibility', 'public')
                ->count(),
            'total_enrollments' => Course::where('created_by', 'admin')->sum('current_enrollment'),
            'completion_rate' => $this->calculateOverallCompletionRate(),
        ];

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'status', 'subject', 'level', 'visibility']),
            'stats' => $stats,
        ]);
    }

    private function calculateOverallCompletionRate(): float
    {
        $totalEnrollments = \App\Models\CourseEnrollment::whereHas('course', function ($q) {
            $q->where('created_by', 'admin');
        })->count();

        $completedEnrollments = \App\Models\CourseEnrollment::whereHas('course', function ($q) {
            $q->where('created_by', 'admin');
        })->where('status', 'completed')->count();

        return $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0;
    }

    public function show(Course $course)
    {
        $course->load([
            'examBoard',
            'creator',
            'modules.topics.contents',
            'modules.topics.quiz',
            'enrollments.user.studentProfile',
            'enrollments' => function ($query) {
                $query->with('user')->latest()->limit(10);
            },
            'certificates.user',
        ]);

        // Calculate enrollment analytics
        $enrollmentStats = [
            'total' => $course->enrollments()->count(),
            'active' => $course->activeEnrollments()->count(),
            'completed' => $course->completedEnrollments()->count(),
            'average_progress' => round($course->enrollments()->avg('progress_percentage') ?? 0, 1),
            'completion_rate' => $course->enrollments()->count() > 0
                ? round(($course->completedEnrollments()->count() / $course->enrollments()->count()) * 100, 1)
                : 0,
        ];

        // Recent enrollments for quick view
        $recentEnrollments = $course->enrollments()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Module stats
        $moduleStats = [
            'total_modules' => $course->modules()->count(),
            'total_topics' => $course->topics()->count(),
            'total_quizzes' => $course->topics()->where('has_quiz', true)->count(),
        ];

        return Inertia::render('Admin/Courses/Show', [
            'course' => $course,
            'enrollmentStats' => $enrollmentStats,
            'recentEnrollments' => $recentEnrollments,
            'moduleStats' => $moduleStats,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Courses/Create', [
            'exam_boards' => ExamBoard::active()->get(),
            'levels' => $this->getLevels(),
            'subjects' => $this->getPopularSubjects(),
        ]);
    }

    private function getLevels()
    {
        return [
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
            'expert' => 'Expert',
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:100',
            'description' => 'required|string',
            'level' => 'required|string|in:beginner,intermediate,advanced',
            'exam_board_id' => 'nullable|exists:exam_boards,id',
            'estimated_duration_hours' => 'required|integer|min:1',
            'learning_objectives' => 'required|array|min:1',
            'learning_objectives.*' => 'string|max:500',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'string|max:500',
            'enrollment_limit' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'is_paid' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'target_completion_date' => 'nullable|date|after:today',
        ]);

        // Prepare course data for generation
        $courseData = [
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'level' => $validated['level'],
            'exam_board_id' => $validated['exam_board_id'] ?? null,
            'estimated_duration_hours' => $validated['estimated_duration_hours'],
            'learning_objectives' => $validated['learning_objectives'],
            'prerequisites' => $validated['prerequisites'] ?? [],
            'enrollment_limit' => $validated['enrollment_limit'] ?? null,
            'price' => $validated['price'] ?? 0,
            'is_paid' => $validated['is_paid'] ?? false,
            'tags' => $validated['tags'] ?? [],
            'target_completion_date' => $validated['target_completion_date'] ?? now()->addMonths(3),
            'created_by' => 'admin',
            'created_by_user_id' => auth()->id(),
            'status' => 'draft',
            'visibility' => 'private',
            'is_public' => false,
        ];

        try {
            // Use the CourseGenerationService to create admin course
            $course = $this->courseGenerationService->generateCourse($courseData);

            return redirect()->route('admin.courses.show', $course->id)
            ->with('success', 'Course created successfully! Content and quizzes are being generated in the background. You will be notified when complete.');
        } catch (\Exception $e) {
            \Log::error('Admin course creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create course: ' . $e->getMessage());
        }
    }

    public function edit(Course $course)
    {


        $course->load(['examBoard', 'creator']);

        $examBoards = ExamBoard::active()->get();

        return Inertia::render('Admin/Courses/Edit', [
            'course' => $course,
            'exam_boards' => $examBoards,
            'levels' => $this->getLevels(),
            'statuses' => ['draft', 'active', 'archived'],
            'visibilities' => ['private', 'public', 'unlisted'],
        ]);
    }

    public function update(Request $request, Course $course)
    {


        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:100',
            'description' => 'required|string',
            'level' => 'required',
            'status' => 'required|in:draft,active,archived',
            'exam_board_id' => 'nullable|exists:exam_boards,id',
            'enrollment_limit' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'is_paid' => 'boolean',
            'visibility' => 'required|in:private,public,unlisted',
            'is_public' => 'boolean',
            'estimated_duration_hours' => 'required|integer|min:1',
            'target_completion_date' => 'nullable|date|after:today',
        ]);

        // Update slug if title changed
        if ($course->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $course->update($validated);

        return redirect()->route('admin.courses.show', $course->id)
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        // Check if there are active enrollments
        if ($course->activeEnrollments()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete course with active enrollments. Please archive it instead.');
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    public function publish(Course $course)
    {
        $course->publish();

        return redirect()->back()->with('success', 'Course published successfully.');
    }

    public function unpublish(Course $course)
    {
        $course->unpublish();

        return redirect()->back()->with('success', 'Course unpublished successfully.');
    }

    public function regenerateContent(Course $course)
    {
        // Ensure admin can only regenerate content for admin-created courses
        if ($course->created_by !== 'admin') {
            abort(404);
        }

        try {
            // Delete existing modules and topics
            $course->modules()->delete();

            // Prepare course data for regeneration
            $courseData = [
                'title' => $course->title,
                'subject' => $course->subject,
                'description' => $course->description,
                'level' => $course->level,
                'learning_objectives' => $course->learning_objectives,
                'prerequisites' => $course->prerequisites,
                'estimated_duration_hours' => $course->estimated_duration_hours,
            ];

            // Regenerate course content
            $this->courseGenerationService->generateAdminCourse($courseData);

            return redirect()->back()->with('success', 'Course content regenerated successfully.');
        } catch (\Exception $e) {
            \Log::error('Course content regeneration failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to regenerate content: ' . $e->getMessage());
        }
    }

    public function enrollments(Course $course)
    {
        // Ensure admin can only view enrollments for admin-created courses
        if ($course->created_by !== 'admin') {
            abort(404);
        }

        $enrollments = $course->enrollments()
            ->with(['user', 'studentProfile'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Courses/Enrollments', [
            'course' => $course,
            'enrollments' => $enrollments,
        ]);
    }

    public function analytics(Course $course)
    {
        // Ensure admin can only view analytics for admin-created courses
        if ($course->created_by !== 'admin') {
            abort(404);
        }

        $analytics = [
            'enrollment_trend' => $this->getEnrollmentTrend($course),
            'progress_distribution' => $this->getProgressDistribution($course),
            'completion_timeline' => $this->getCompletionTimeline($course),
            'popular_modules' => $this->getPopularModules($course),
        ];

        return Inertia::render('Admin/Courses/Analytics', [
            'course' => $course,
            'analytics' => $analytics,
        ]);
    }

    private function getEnrollmentTrend(Course $course)
    {
        return $course->enrollments()
            ->selectRaw('DATE(enrolled_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getProgressDistribution(Course $course)
    {
        return $course->enrollments()
            ->selectRaw('FLOOR(progress_percentage / 25) * 25 as range_start, COUNT(*) as count')
            ->groupBy('range_start')
            ->orderBy('range_start')
            ->get()
            ->map(function ($item) {
                return [
                    'range' => $item->range_start . '% - ' . ($item->range_start + 25) . '%',
                    'count' => $item->count,
                ];
            });
    }

    private function getCompletionTimeline(Course $course)
    {
        return $course->completedEnrollments()
            ->selectRaw('DATE(completed_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPopularModules(Course $course)
    {
        // This would require additional tracking in your student modules
        // For now, return basic module stats
        return $course->modules()
            ->withCount(['topics', 'studentModules as completed_count' => function ($q) {
                $q->where('is_completed', true);
            }])
            ->orderBy('order')
            ->get()
            ->map(function ($module) {
                return [
                    'title' => $module->title,
                    'total_topics' => $module->topics_count,
                    'completion_rate' => $module->topics_count > 0
                        ? round(($module->completed_count / $module->topics_count) * 100, 1)
                        : 0,
                ];
            });
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


    protected static function getLevelCode(string $level): string
    {
        $levelMap = [
            'beginner' => 'BEG',
            'intermediate' => 'INT',
            'advanced' => 'ADV',
            'expert' => 'EXP',
            'foundation' => 'FND',
            'higher' => 'HIG',
            'standard' => 'STD',
            'extended' => 'EXT',
            'elementary' => 'ELE',
            'secondary' => 'SEC',
            'tertiary' => 'TER',
            'graduate' => 'GRD',
            'postgraduate' => 'PGD',
            'professional' => 'PRO',
        ];

        $levelLower = strtolower($level);
        return $levels[$levelLower] ?? substr(strtoupper($level), 0, 3);
    }


    public function outline(Course $course)
    {
        // Ensure admin can only view outline for admin-created courses
        if ($course->created_by !== 'admin') {
            abort(404);
        }

        $course->load([
            'modules' => function ($query) {
                $query->orderBy('order');
            },
            'modules.topics' => function ($query) {
                $query->orderBy('order');
            },
            'modules.topics.contents' => function ($query) {
                $query->orderBy('order');
            },
            'modules.topics.quiz' => function ($query) {
                $query->select('id', 'course_outline_id', 'title', 'is_active','updated_at');
            },
            'examBoard',
            'creator',
        ]);

        // Calculate outline statistics
        $stats = [
            'total_modules' => $course->modules->count(),
            'total_topics' => $course->modules->sum(function ($module) {
                return $module->topics->count();
            }),
            'total_content_items' => $course->modules->sum(function ($module) {
                return $module->topics->sum(function ($topic) {
                    return $topic->contents->count();
                });
            }),
            'total_quizzes' => $course->modules->sum(function ($module) {
                return $module->topics->filter(function ($topic) {
                    return $topic->has_quiz && $topic->quiz;
                })->count();
            }),
            'total_duration_minutes' => $course->modules->sum('estimated_duration_minutes'),
            'total_duration_hours' => ceil($course->modules->sum('estimated_duration_minutes') / 60),
        ];

        // Group content by type
        $contentByType = [];
        foreach ($course->modules as $module) {
            foreach ($module->topics as $topic) {
                foreach ($topic->contents as $content) {
                    $type = $content->content_type;
                    $contentByType[$type] = ($contentByType[$type] ?? 0) + 1;
                }
            }
        }

        return Inertia::render('Admin/Courses/Outline', [
            'course' => $course,
            'stats' => $stats,
            'contentByType' => $contentByType,
        ]);
    }

    public function generateAllContent(Course $course)
    {
        if ($course->created_by !== 'admin') {
            abort(404);
        }

        try {
            // Get topics that need content generation - FIXED QUERY
            $pendingTopics = $course->topics()
                ->where('course_outlines.needs_content_generation', true) // Specify table
                ->get();

            if ($pendingTopics->isEmpty()) {
                return redirect()->back()
                    ->with('info', 'No topics require content generation at this time.');
            }

            // Create batch jobs with validation
            $jobs = [];
            foreach ($pendingTopics as $topic) {
                if (!$topic->exists || !$topic->id) {
                    Log::warning('Skipping invalid topic in batch generation', [
                        'topic_id' => $topic->id ?? 'null',
                        'course_id' => $course->id
                    ]);
                    continue;
                }

                // Reload to ensure fresh model
                $freshTopic = CourseOutline::find($topic->id);
                if (!$freshTopic) {
                    Log::warning('Topic not found, skipping', ['topic_id' => $topic->id]);
                    continue;
                }

                $jobs[] = new GenerateCourseContentJob($freshTopic);
            }

            if (empty($jobs)) {
                return redirect()->back()
                    ->with('error', 'No valid topics found for content generation.');
            }


            // Start batch processing
            $batch = Bus::batch($jobs)
                ->name("Content Generation - Course #{$course->id}")
                ->allowFailures()
                ->onQueue('course_generation')
                ->dispatch();

            return redirect()->back()
                ->with('success', "Started generating content for " . count($jobs) . " topics in the background.")
                ->with('batch_id', $batch->id);

        } catch (\Exception $e) {
            Log::error('Failed to start batch content generation', [
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to start content generation: ' . $e->getMessage());
        }
    }

    /**
     * Generate capstone project using background job
     */
    public function generateCapstoneProject(Course $course, Request $request)
    {
        try {
            Log::info('Starting capstone project generation with job', [
                'course_id' => $course->id,
                'course_title' => $course->title
            ]);

            // Find or create capstone project module
            $capstoneModule = $this->findOrCreateCapstoneModule($course);

            // Find or create capstone topic
            $capstoneTopic = $this->findOrCreateCapstoneTopic($capstoneModule, $course);

            // Dispatch capstone project generation job
            $batch = Bus::batch([
                new GenerateCapstoneProjectJob($course, $capstoneTopic)
            ])
            ->name("Capstone Project Generation - Course #{$course->id}")
            ->onQueue('course_generation')
            ->catch(function ($batch, $e) use ($course) {
                Log::error('Capstone project generation failed', [
                    'course_id' => $course->id,
                    'batch_id' => $batch->id,
                    'error' => $e->getMessage()
                ]);
            })
            ->then(function ($batch) use ($course) {
                Log::info('Capstone project generation completed', [
                    'course_id' => $course->id,
                    'batch_id' => $batch->id
                ]);

                $course->update([
                    'capstone_generated_at' => now(),
                    'capstone_generation_status' => 'completed',
                ]);
            })
            ->dispatch();

            // Update course status
            $course->update([
                'capstone_generation_status' => 'processing',
                'capstone_generation_started_at' => now(),
                'capstone_batch_id' => $batch->id,
            ]);

            Log::info('Capstone project generation job dispatched', [
                'course_id' => $course->id,
                'batch_id' => $batch->id,
                'topic_id' => $capstoneTopic->id
            ]);

            return redirect()->back()
                ->with('success', 'Capstone project generation started in the background.')
                ->with('batch_id', $batch->id)
                ->with('capstone_topic_id', $capstoneTopic->id);

        } catch (\Exception $e) {
            Log::error('Failed to start capstone project generation', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to start capstone project generation: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate entire course outline
     */
    public function regenerateOutline(Course $course, Request $request)
    {
        try {
            $preserveContent = $request->boolean('preserve_content', false);

            Log::info('Starting course outline regeneration', [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'preserve_content' => $preserveContent
            ]);

            DB::beginTransaction();

            // Backup existing modules if preserving content
            $backupData = null;
            if ($preserveContent) {
                $backupData = $this->backupCourseContent($course);
            }

            // Delete existing modules and topics
            $deletedCount = $course->modules()->delete();

            Log::info('Deleted existing modules', [
                'course_id' => $course->id,
                'deleted_count' => $deletedCount
            ]);

            // Prepare course data for regeneration
            $courseData = [
                'title' => $course->title,
                'subject' => $course->subject,
                'description' => $course->description,
                'level' => $course->level,
                'learning_objectives' => $course->learning_objectives,
                'prerequisites' => $course->prerequisites,
                'estimated_duration_hours' => $course->estimated_duration_hours,
                'is_public' => $course->is_public,
                'visibility' => $course->visibility,
                'created_by_user_id' => $course->created_by_user_id,
                'created_by' => 'admin',
            ];

            // Use the course generation service to create new outline
            $updatedCourse = $this->courseGenerationService->updateCourseContent($course, [
                'regenerate_outline' => true,
                ...$courseData
            ]);

            // Restore content if requested
            if ($preserveContent && $backupData) {
                $this->restoreCourseContent($updatedCourse, $backupData);
            }

            // Update course status and timestamps
            $course->update([
                'outline_regenerated_at' => now(),
                'outline_regeneration_count' => ($course->outline_regeneration_count ?? 0) + 1,
                'status' => 'draft', // Set back to draft for review
                'needs_content_generation' => true, // Flag that content needs to be regenerated
            ]);

            DB::commit();

            Log::info('Course outline regenerated successfully', [
                'course_id' => $course->id,
                'new_module_count' => $updatedCourse->modules()->count(),
                'preserve_content' => $preserveContent
            ]);

            return redirect()->route('admin.courses.outline', $course->id)
                ->with('success', 'Course outline regenerated successfully! ' .
                       ($preserveContent ? 'Existing content was preserved.' : ''))
                ->with('regenerated_course', $updatedCourse);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Course outline regeneration failed', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to regenerate course outline: ' . $e->getMessage());
        }
    }

    /**
     * Get batch progress for monitoring
     */
    public function batchProgress(Course $course, $batchId, Request $request)
    {
        try {
            $batch = Bus::findBatch($batchId);

            if (!$batch) {
                return response()->json([
                    'error' => 'Batch not found',
                    'batch_id' => $batchId
                ], 404);
            }

            $progress = [
                'batch_id' => $batch->id,
                'total_jobs' => $batch->totalJobs,
                'pending_jobs' => $batch->pendingJobs,
                'failed_jobs' => $batch->failedJobs,
                'processed_jobs' => $batch->processedJobs(),
                'progress' => $batch->progress(),
                'finished_at' => $batch->finishedAt,
                'cancelled_at' => $batch->cancelledAt,
                'failed_job_ids' => $batch->failedJobIds,
            ];

            return response()->json($progress);

        } catch (\Exception $e) {
            Log::error('Failed to get batch progress', [
                'course_id' => $course->id,
                'batch_id' => $batchId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to get batch progress',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ============================
    // HELPER METHODS
    // ============================

    /**
     * Get pending topics for generation
     */
    private function getPendingTopics(Course $course, string $contentType = 'all'): Collection
    {
        $query = $course->topics()
            ->where('course_outlines.needs_content_generation', true)
            ->with(['module'])
            ->orderBy('course_outlines.module_id')
            ->orderBy('course_outlines.order');

        // Filter by content type if specified
        if ($contentType !== 'all') {
            if ($contentType === 'lessons') {
                $query->whereIn('course_outlines.type', ['topic', 'lesson', 'text']);
            } elseif ($contentType === 'quizzes') {
                $query->where('course_outlines.has_quiz', true)
                      ->where('course_outlines.needs_quiz_generation', true);
            } elseif ($contentType === 'projects') {
                $query->where('course_outlines.has_project', true);
            }
        }

        return $query->get();
    }

    /**
     * Get pending quizzes
     */
    private function getPendingQuizzes(Course $course): Collection
    {
        return $course->topics()
            ->where('course_outlines.has_quiz', true)
            ->where('course_outlines.needs_quiz_generation', true)
            ->with(['module'])
            ->orderBy('course_outlines.module_id')
            ->orderBy('course_outlines.order')
            ->get();
    }

    /**
     * Find or create capstone module
     */
    private function findOrCreateCapstoneModule(Course $course): Module
    {
        $capstoneModule = $course->modules()
            ->where(function($query) {
                $query->where('title', 'like', '%capstone%')
                      ->orWhere('title', 'like', '%project%')
                      ->orWhere('title', 'like', '%final%');
            })
            ->first();

        if (!$capstoneModule) {
            $capstoneModule = Module::create([
                'course_id' => $course->id,
                'title' => 'Capstone Project',
                'description' => 'Final project to demonstrate mastery of course concepts',
                'order' => $course->modules()->count() + 1,
                'estimated_duration_minutes' => 300,
                'learning_objectives' => ['Apply all learned concepts to a real-world project'],
                'needs_content_generation' => true,
            ]);
        }

        return $capstoneModule;
    }

    /**
     * Find or create capstone topic
     */
    private function findOrCreateCapstoneTopic(Module $module, Course $course): CourseOutline
    {
        $capstoneTopic = $module->topics()
            ->where('has_project', true)
            ->orWhere('title', 'like', '%capstone%')
            ->orWhere('title', 'like', '%project%')
            ->orWhere('type', 'project')
            ->first();

        if (!$capstoneTopic) {
            $capstoneTopic = CourseOutline::create([
                'module_id' => $module->id,
                'title' => 'Capstone Project: ' . $course->title,
                'description' => 'Apply all the knowledge and skills you\'ve gained throughout this course to create a comprehensive final project.',
                'order' => 1,
                'type' => 'project',
                'estimated_duration_minutes' => 300,
                'learning_objectives' => ['Demonstrate comprehensive understanding of course material'],
                'key_concepts' => $this->extractKeyConceptsFromCourse($course),
                'has_project' => true,
                'needs_content_generation' => true,
            ]);
        }

        return $capstoneTopic;
    }

    /**
     * Backup course content before regeneration
     */
    private function backupCourseContent(Course $course): array
    {
        $backup = [
            'topics' => [],
            'contents' => [],
            'quizzes' => [],
        ];

        foreach ($course->modules as $module) {
            foreach ($module->topics as $topic) {
                $backup['topics'][] = [
                    'original_title' => $topic->title,
                    'title' => $topic->title,
                    'description' => $topic->description,
                    'type' => $topic->type,
                    'has_quiz' => $topic->has_quiz,
                    'has_project' => $topic->has_project,
                    'contents' => $topic->contents->toArray(),
                    'quiz' => $topic->quiz ? $topic->quiz->toArray() : null,
                ];
            }
        }

        return $backup;
    }

    /**
     * Restore course content after regeneration
     */
    private function restoreCourseContent(Course $course, array $backupData): void
    {
        foreach ($course->modules as $module) {
            foreach ($module->topics as $topic) {
                // Try to find matching topic in backup
                $backupTopic = collect($backupData['topics'])->first(function ($backup) use ($topic) {
                    similar_text(strtolower($backup['title']), strtolower($topic->title), $percent);
                    return $percent > 70;
                });

                if ($backupTopic) {
                    if (!empty($backupTopic['contents'])) {
                        foreach ($backupTopic['contents'] as $contentData) {
                            $topic->contents()->create([
                                'content' => $contentData['content'],
                                'content_type' => $contentData['content_type'],
                                'generated_at' => $contentData['generated_at'] ?? now(),
                            ]);
                        }

                        $topic->update([
                            'needs_content_generation' => false,
                            'content_generated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Extract key concepts from course for capstone project
     */
    private function extractKeyConceptsFromCourse(Course $course): array
    {
        $concepts = [];

        foreach ($course->modules as $module) {
            foreach ($module->topics as $topic) {
                if (!empty($topic->key_concepts)) {
                    $concepts = array_merge($concepts, $topic->key_concepts);
                }
            }
        }

        return array_unique($concepts);
    }

    /**
     * Update course generation statistics
     */
    private function updateCourseGenerationStats(Course $course): void
    {
        $totalTopics = $course->topics()->count();
        $generatedTopics = $course->topics()
            ->where('course_outlines.needs_content_generation', false)
            ->count();
        $pendingTopics = $totalTopics - $generatedTopics;

        $course->update([
            'content_generation_progress' => $totalTopics > 0 ? ($generatedTopics / $totalTopics) * 100 : 0,
            'pending_content_count' => $pendingTopics,
            'last_content_generation_batch' => now(),
        ]);
    }


}
