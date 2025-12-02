<?php
// app/Http\Controllers\Student/DashboardController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use App\Models\ProgressTracking;
use App\Models\QuizAttempt;
use App\Services\ProgressTrackingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DashboardController extends Controller
{
    protected $progressService;

    public function __construct(ProgressTrackingService $progressService)
    {
        $this->progressService = $progressService;
    }

    public function index()
    {
        try {
            $student = auth()->user();
            $studentProfile = $student->studentProfile;

            $stats = $this->getStudentStats($student);
            $activeCourse = $this->getActiveCourse($student);
            $recentActivity = $this->getRecentActivity($student);
            $upcomingDeadlines = $this->getUpcomingDeadlines($student);
            $learningAnalytics = $this->progressService->getLearningAnalytics($student);

            // Get current subscription
            $currentSubscription = $student->current_subscription;

            return Inertia::render('Student/Dashboard', [
                'stats' => $stats,
                'active_course' => $activeCourse,
                'recent_activity' => $recentActivity,
                'upcoming_deadlines' => $upcomingDeadlines,
                'learning_analytics' => $learningAnalytics,
                'student_profile' => $studentProfile,
                'current_subscription' => $currentSubscription, 
            ]);
        } catch (\Exception $e) {
            // Log the error and return safe defaults
            \Log::error('DashboardController error: ' . $e->getMessage());

            return Inertia::render('Student/Dashboard', [
                'stats' => $this->getDefaultStats(),
                'active_course' => null,
                'recent_activity' => [],
                'upcoming_deadlines' => ['courses' => [], 'quizzes' => []],
                'learning_analytics' => [],
                'student_profile' => null,
                'current_subscription' => null,
                'showOnboarding' => $user->shouldSeeOnboarding(),
            ]);
        }
    }

    private function getStudentStats($student)
    {
        try {
            $totalCourses = $student->courses()->count();
            $completedCourses = $student->courses()->where('status', 'completed')->count();
            $activeCourses = $student->courses()->where('status', 'active')->count();

            $totalStudyTime = ProgressTracking::where('user_id', $student->id)
                ->sum('time_spent_minutes') ?? 0;

            $averageQuizScore = QuizAttempt::where('user_id', $student->id)
                ->whereNotNull('percentage')
                ->avg('percentage') ?? 0;

            // Calculate completion rate using the new progress service
            $completionRate = 0;
            if ($totalCourses > 0) {
                $totalProgress = 0;
                $student->courses->each(function ($course) use (&$totalProgress) {
                    $progress = $this->progressService->calculateCourseProgress($course);
                    $totalProgress += $progress['overall_completion_percentage'];
                });
                $completionRate = round($totalProgress / $totalCourses, 1);
            }

            return [
                'total_courses' => $totalCourses,
                'completed_courses' => $completedCourses,
                'active_courses' => $activeCourses,
                'total_study_time' => round($totalStudyTime / 60, 1), // Convert to hours
                'average_quiz_score' => round($averageQuizScore, 1),
                'completion_rate' => $completionRate,
            ];
        } catch (\Exception $e) {
            \Log::error('Error in getStudentStats: ' . $e->getMessage());
            return $this->getDefaultStats();
        }
    }

    private function getDefaultStats()
    {
        return [
            'total_courses' => 0,
            'completed_courses' => 0,
            'active_courses' => 0,
            'total_study_time' => 0,
            'average_quiz_score' => 0,
            'completion_rate' => 0,
        ];
    }

    private function getActiveCourse($student)
    {
        try {
            $activeCourse = $student->courses()
                ->with(['modules' => function ($query) {
                    $query->with(['topics' => function ($query) {
                        $query->where('is_completed', false)
                            ->orderBy('order')
                            ->limit(3);
                    }])
                    ->orderBy('order');
                }])
                ->where('status', 'active')
                ->first();

            if (!$activeCourse) {
                return null;
            }

            // Calculate progress using the service
            $courseProgress = $this->progressService->calculateCourseProgress($activeCourse);

            // Get next topic to study - FIXED: Now properly traverses course→modules→topics
            $nextTopic = null;
            foreach ($activeCourse->modules as $module) {
                $pendingTopics = $module->topics->where('is_completed', false);
                if ($pendingTopics->count() > 0) {
                    $nextTopic = $pendingTopics->first();
                    break;
                }
            }

            // Get upcoming topics - FIXED: Properly gets pending topics across all modules
            $upcomingTopics = collect();
            foreach ($activeCourse->modules as $module) {
                $moduleTopics = $module->topics()
                    ->where('is_completed', false)
                    ->orderBy('order')
                    ->get()
                    ->map(function ($topic) use ($module) {
                        return [
                            'id' => $topic->id,
                            'title' => $topic->title,
                            'module_title' => $module->title,
                            'order' => $topic->order,
                            'estimated_duration_minutes' => $topic->estimated_duration_minutes,
                        ];
                    });
                $upcomingTopics = $upcomingTopics->merge($moduleTopics);
            }

            return [
                'id' => $activeCourse->id,
                'title' => $activeCourse->title,
                'subject' => $activeCourse->subject,
                'description' => $activeCourse->description,
                'progress' => $courseProgress,
                'next_topic' => $nextTopic ? [
                    'id' => $nextTopic->id,
                    'title' => $nextTopic->title,
                    'module_title' => $nextTopic->module->title,
                    'estimated_duration_minutes' => $nextTopic->estimated_duration_minutes,
                ] : null,
                'upcoming_topics' => $upcomingTopics->take(5),
            ];
        } catch (\Exception $e) {
            \Log::error('Error in getActiveCourse: ' . $e->getMessage());
            return null;
        }
    }

    private function getRecentActivity($student)
    {
        try {
            $activities = ProgressTracking::with(['course', 'courseOutline.module.course'])
                ->where('user_id', $student->id)
                ->latest()
                ->limit(10)
                ->get();

            return $activities->map(function ($activity) {
                $moduleTitle = 'Unknown Module';
                $courseTitle = 'Unknown Course';

                // FIXED: Properly traverse the relationship chain
                if ($activity->courseOutline && $activity->courseOutline->module) {
                    $moduleTitle = $activity->courseOutline->module->title;
                    if ($activity->courseOutline->module->course) {
                        $courseTitle = $activity->courseOutline->module->course->title;
                    }
                } elseif ($activity->course) {
                    // Fallback to direct course relationship
                    $courseTitle = $activity->course->title;
                }

                return [
                    'id' => $activity->id,
                    'course_title' => $courseTitle,
                    'module_title' => $moduleTitle,
                    'activity_type' => $activity->activity_type ?? 'unknown',
                    'topic_title' => $activity->courseOutline->title ?? 'Unknown Topic',
                    'time_spent' => $activity->time_spent_minutes ?? 0,
                    'is_completed' => $activity->is_completed ?? false,
                    'score' => $activity->completion_score ?? null,
                    'created_at' => $activity->created_at ? $activity->created_at->diffForHumans() : 'Unknown',
                ];
            });
        } catch (\Exception $e) {
            \Log::error('Error in getRecentActivity: ' . $e->getMessage());
            return [];
        }
    }

    private function getUpcomingDeadlines($student)
    {
        $courses = [];
        $quizzes = [];

        try {
            // FIXED: Added table prefix to avoid ambiguous column error
            $courses = $student->courses()
                ->where('courses.status', 'active') // Specify table
                ->where('courses.target_completion_date', '>=', now()) // Specify table
                ->orderBy('courses.target_completion_date') // Specify table
                ->limit(5)
                ->get()
                ->map(function ($course) {
                    $daysRemaining = $course->target_completion_date ?
                        now()->diffInDays($course->target_completion_date, false) : 0;

                    // Calculate progress using the service
                    $progress = $this->progressService->calculateCourseProgress($course);

                    return [
                        'id' => $course->id,
                        'title' => $course->title ?? 'Unknown Course',
                        'deadline' => $course->target_completion_date ?
                            $course->target_completion_date->format('M j, Y') : 'No deadline',
                        'days_remaining' => $daysRemaining,
                        'progress_percentage' => $progress['overall_completion_percentage'],
                        'is_urgent' => $daysRemaining <= 7,
                        'modules_completed' => $progress['completed_modules'],
                        'total_modules' => $progress['total_modules'],
                    ];
                });
        } catch (\Exception $e) {
            \Log::error('Error in getUpcomingDeadlines - courses: ' . $e->getMessage());
            $courses = [];
        }

        try {
            // Get quiz deadlines - FIXED: Updated relationship chain
            $quizzes = QuizAttempt::with(['quiz.courseOutline.module.course'])
                ->where('user_id', $student->id)
                ->whereNull('completed_at')
                ->whereNotNull('started_at')
                ->where('started_at', '>=', now()->subDays(1))
                ->get()
                ->map(function ($attempt) {
                    $timeLimit = $attempt->quiz->time_limit_minutes ?? 0;
                    $startedAt = $attempt->started_at;
                    $dueAt = $startedAt ? $startedAt->addMinutes($timeLimit) : now();

                    $courseTitle = 'Unknown Course';
                    $moduleTitle = 'Unknown Module';
                    $topicTitle = 'Unknown Topic';

                    // FIXED: Proper relationship traversal
                    if ($attempt->quiz && $attempt->quiz->courseOutline) {
                        $topicTitle = $attempt->quiz->courseOutline->title;
                        if ($attempt->quiz->courseOutline->module) {
                            $moduleTitle = $attempt->quiz->courseOutline->module->title;
                            if ($attempt->quiz->courseOutline->module->course) {
                                $courseTitle = $attempt->quiz->courseOutline->module->course->title;
                            }
                        }
                    }

                    return [
                        'id' => $attempt->id,
                        'quiz_title' => $attempt->quiz->title ?? 'Unknown Quiz',
                        'course_title' => $courseTitle,
                        'module_title' => $moduleTitle,
                        'topic_title' => $topicTitle,
                        'due_at' => $dueAt->format('M j, Y H:i'),
                        'is_overdue' => $dueAt->isPast(),
                        'time_remaining_minutes' => $dueAt->isPast() ? 0 : max(0, now()->diffInMinutes($dueAt)),
                    ];
                });
        } catch (\Exception $e) {
            \Log::error('Error in getUpcomingDeadlines - quizzes: ' . $e->getMessage());
            $quizzes = [];
        }

        return [
            'courses' => $courses,
            'quizzes' => $quizzes,
        ];
    }

    public function getProgressChart(Request $request)
    {
        try {
            $student = auth()->user();
            $period = $request->get('period', '7d');

            switch ($period) {
                case '30d':
                    $days = 30;
                    break;
                case '90d':
                    $days = 90;
                    break;
                default:
                    $days = 7;
            }

            $progressData = ProgressTracking::where('user_id', $student->id)
                ->where('created_at', '>=', now()->subDays($days))
                ->selectRaw('DATE(created_at) as date, SUM(time_spent_minutes) as study_time')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Get course progress using the new service
            $courseProgress = $student->courses()
                ->where('status', 'active')
                ->get()
                ->map(function ($course) {
                    $progress = $this->progressService->calculateCourseProgress($course);
                    return [
                        'title' => $course->title,
                        'progress_percentage' => $progress['overall_completion_percentage'],
                        'module_progress' => $progress['module_completion_percentage'],
                        'topic_progress' => $progress['topic_completion_percentage'],
                    ];
                });

            return response()->json([
                'study_time' => $progressData,
                'course_progress' => $courseProgress,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getProgressChart: ' . $e->getMessage());

            return response()->json([
                'study_time' => [],
                'course_progress' => [],
            ]);
        }
    }

    /**
     * Get module progress for a specific course
     */
    public function getCourseModuleProgress(Course $course)
    {
        try {
            //$this->authorize('view', $course);

            $progress = $this->progressService->calculateCourseProgress($course);

            return response()->json([
                'progress' => $progress,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getCourseModuleProgress: ' . $e->getMessage());

            return response()->json([
                'progress' => [
                    'completed_modules' => 0,
                    'total_modules' => 0,
                    'completed_topics' => 0,
                    'total_topics' => 0,
                    'module_completion_percentage' => 0,
                    'topic_completion_percentage' => 0,
                    'overall_completion_percentage' => 0,
                ],
            ]);
        }
    }
}
