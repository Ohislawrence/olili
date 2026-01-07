<?php
// app/Http\Controllers\Student/DashboardController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
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

        // Get recently dropped courses
        $recentlyDroppedCourses = $this->getRecentlyDroppedCourses($student);

        // Get current subscription
        $currentSubscription = $student->current_subscription;

        return Inertia::render('Student/Dashboard', [
            'stats' => $stats,
            'active_course' => $activeCourse,
            'recent_activity' => $recentActivity,
            'upcoming_deadlines' => $upcomingDeadlines,
            'learning_analytics' => $learningAnalytics,
            'recently_dropped_courses' => $recentlyDroppedCourses, // Add this
            'student_profile' => $studentProfile,
            'current_subscription' => $currentSubscription,
        ]);
    } catch (\Exception $e) {
        \Log::error('DashboardController error: ' . $e->getMessage());

        return Inertia::render('Student/Dashboard', [
            'stats' => $this->getDefaultStats(),
            'active_course' => null,
            'recent_activity' => [],
            'upcoming_deadlines' => ['courses' => [], 'quizzes' => []],
            'learning_analytics' => [],
            'recently_dropped_courses' => [], // Add this
            'student_profile' => null,
            'current_subscription' => null,
        ]);
    }
}

    private function getStudentStats($student)
    {
        try {
            // Get through enrollments - EXCLUDE DROPPED courses
            $totalCourses = $student->courseEnrollments()
                ->where('status', '!=', 'dropped')
                ->count();

            $completedCourses = $student->courseEnrollments()
                ->where('status', 'completed')
                ->count();

            $activeCourses = $student->courseEnrollments()
                ->whereIn('status', ['enrolled', 'active'])
                ->count();

            $totalStudyTime = ProgressTracking::where('user_id', $student->id)
                ->sum('time_spent_minutes') ?? 0;

            $averageQuizScore = QuizAttempt::where('user_id', $student->id)
                ->whereNotNull('percentage')
                ->avg('percentage') ?? 0;

            // Calculate completion rate using the new progress service
            $completionRate = 0;
            if ($totalCourses > 0) {
                $totalProgress = 0;
                // Get courses through enrollments (excluding dropped)
                $enrollments = $student->courseEnrollments()
                    ->where('status', '!=', 'dropped')
                    ->with('course')
                    ->get();

                foreach ($enrollments as $enrollment) {
                    $progress = $this->progressService->calculateCourseProgress($enrollment->course, $student->id);
                    $totalProgress += $progress['overall_completion_percentage'];
                }
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
            // Get active enrollment with course data - EXCLUDE DROPPED courses
            $activeEnrollment = $student->courseEnrollments()
                ->whereIn('status', ['enrolled', 'active'])
                ->with([
                    'course.examBoard',
                    'course.modules' => function ($query) {
                        $query->with(['topics' => function ($query) {
                            $query->orderBy('order');
                        }])
                        ->orderBy('order');
                    }
                ])
                ->orderBy('updated_at', 'desc') // Most recently active first
                ->first();

            if (!$activeEnrollment || !$activeEnrollment->course) {
                return null;
            }

            $course = $activeEnrollment->course;

            // Calculate progress using the service
            $courseProgress = $this->progressService->calculateCourseProgress($course, $student->id);

            // Get next topic to study using ProgressTrackingService
            $nextTopic = null;
            $modules = $course->modules()->with(['topics'])->orderBy('order')->get();

            foreach ($modules as $module) {
                foreach ($module->topics as $topic) {
                    // Check if topic is completed for this user using ProgressTrackingService
                    $isCompleted = $this->progressService->isTopicCompletedForEnrollment($activeEnrollment, $topic->id);
                    if (!$isCompleted) {
                        $nextTopic = $topic;
                        break 2; // Break out of both loops
                    }
                }
            }

            // Get upcoming topics
            $upcomingTopics = collect();
            foreach ($modules as $module) {
                foreach ($module->topics as $topic) {
                    // Check if topic is completed using ProgressTrackingService
                    $isCompleted = $this->progressService->isTopicCompletedForEnrollment($activeEnrollment, $topic->id);
                    if (!$isCompleted) {
                        $upcomingTopics->push([
                            'id' => $topic->id,
                            'title' => $topic->title,
                            'module_title' => $module->title,
                            'order' => $topic->order,
                            'estimated_duration_minutes' => $topic->estimated_duration_minutes,
                        ]);

                        if ($upcomingTopics->count() >= 5) {
                            break 2; // Break out of both loops when we have 5 topics
                        }
                    }
                }
            }

            return [
                'id' => $course->id,
                'enrollment_id' => $activeEnrollment->id,
                'title' => $course->title,
                'subject' => $course->subject,
                'description' => $course->description,
                'progress' => $courseProgress,
                'next_topic' => $nextTopic ? [
                    'id' => $nextTopic->id,
                    'title' => $nextTopic->title,
                    'module_title' => $nextTopic->module->title,
                    'estimated_duration_minutes' => $nextTopic->estimated_duration_minutes,
                ] : null,
                'upcoming_topics' => $upcomingTopics,
            ];
        } catch (\Exception $e) {
            \Log::error('Error in getActiveCourse: ' . $e->getMessage());
            return null;
        }
    }

    private function getRecentActivity($student)
    {
        try {
            $activities = ProgressTracking::with(['course', 'courseOutline.module'])
                ->where('user_id', $student->id)
                ->latest()
                ->limit(10)
                ->get();

            return $activities->map(function ($activity) {
                $moduleTitle = 'Unknown Module';
                $courseTitle = $activity->course->title ?? 'Unknown Course';

                // Get module title from course outline
                if ($activity->courseOutline && $activity->courseOutline->module) {
                    $moduleTitle = $activity->courseOutline->module->title;
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
            // Get courses with upcoming deadlines through enrollments - EXCLUDE DROPPED
            $enrollments = $student->courseEnrollments()
                ->where('status', '!=', 'dropped')
                ->with(['course' => function($query) {
                    $query->where('target_completion_date', '>=', now())
                        ->orderBy('target_completion_date');
                }])
                ->whereHas('course', function($query) {
                    $query->where('target_completion_date', '>=', now());
                })
                ->limit(5)
                ->get();

            foreach ($enrollments as $enrollment) {
                $course = $enrollment->course;
                if (!$course) continue;

                $daysRemaining = $course->target_completion_date ?
                    now()->diffInDays($course->target_completion_date, false) : 0;

                // Calculate progress using the service
                $progress = $this->progressService->calculateCourseProgress($course, $student->id);

                $courses[] = [
                    'id' => $course->id,
                    'enrollment_id' => $enrollment->id,
                    'title' => $course->title ?? 'Unknown Course',
                    'deadline' => $course->target_completion_date ?
                        $course->target_completion_date->format('M j, Y') : 'No deadline',
                    'days_remaining' => $daysRemaining,
                    'progress_percentage' => $progress['overall_completion_percentage'],
                    'is_urgent' => $daysRemaining <= 7,
                    'modules_completed' => $progress['completed_modules'],
                    'total_modules' => $progress['total_modules'],
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Error in getUpcomingDeadlines - courses: ' . $e->getMessage());
            $courses = [];
        }

        try {
            // Get quiz deadlines
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

                    // Traverse relationships
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

            // Get course progress using the new service - EXCLUDE DROPPED courses
            $courseProgress = collect();
            $enrollments = $student->courseEnrollments()
                ->where('status', '!=', 'dropped')
                ->with('course')
                ->whereIn('status', ['enrolled', 'active'])
                ->get();

            foreach ($enrollments as $enrollment) {
                $progress = $this->progressService->calculateCourseProgress($enrollment->course, $student->id);
                $courseProgress->push([
                    'title' => $enrollment->course->title,
                    'progress_percentage' => $progress['overall_completion_percentage'],
                    'module_progress' => $progress['module_completion_percentage'],
                    'topic_progress' => $progress['topic_completion_percentage'],
                ]);
            }

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
            $student = auth()->user();

            // Check if student is enrolled in this course (not dropped)
            $enrollment = $student->courseEnrollments()
                ->where('course_id', $course->id)
                ->where('status', '!=', 'dropped')
                ->first();

            if (!$enrollment) {
                throw new \Exception('Student not enrolled in this course');
            }

            $progress = $this->progressService->calculateCourseProgress($course, $student->id);

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

    private function getRecentlyDroppedCourses($student)
    {
        return $student->courseEnrollments()
            ->where('status', 'dropped')
            ->where('dropped_at', '>=', now()->subDays(30))
            ->with('course')
            ->limit(3)
            ->get()
            ->map(function ($enrollment) {
                return [
                    'title' => $enrollment->course->title,
                    'dropped_at' => $enrollment->dropped_at->diffForHumans(),
                    'can_reenroll' => $enrollment->course->canEnroll($student),
                ];
            });
    }
}
