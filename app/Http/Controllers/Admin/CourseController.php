<?php
// app/Http/Controllers/Admin/CourseController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\StudentProfile;
use App\Models\ExamBoard;
use App\Services\CourseGenerationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    protected $courseGenerationService;

    public function __construct(CourseGenerationService $courseGenerationService)
    {
        $this->courseGenerationService = $courseGenerationService;
    }

    public function index(Request $request)
    {
        $query = Course::with(['studentProfile.user', 'examBoard'])
            ->withCount(['outlines', 'outlines as completed_outlines_count' => function ($q) {
                $q->where('is_completed', true);
            }]);

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('subject', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhereHas('studentProfile.user', function ($q) use ($request) {
                      $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                  });
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

        $courses = $query->latest()->paginate(20);

        // Calculate stats safely
        $totalCourses = Course::count();
        $activeCourses = Course::where('status', 'active')->count();
        $completedCourses = Course::where('status', 'completed')->count();

        $stats = [
            'total_courses' => $totalCourses,
            'active_courses' => $activeCourses,
            'completion_rate' => $totalCourses > 0 ? round(($completedCourses / $totalCourses) * 100, 1) : 0,
            'avg_progress' => round(Course::avg('progress_percentage') ?? 0, 1),
        ];

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'status', 'subject', 'level']),
            'stats' => $stats,
        ]);
    }

    public function show(Course $course)
    {
        $course->load([
            'studentProfile.user',
            'examBoard',
            'outlines.contents',
            'outlines.quiz',
            'capstoneProject',
            'chatSessions' => function ($query) {
                $query->with('messages')->latest()->limit(5);
            },
            'progressTracking.user',
        ]);

        $stats = [
            'total_outlines' => $course->outlines->count(),
            'completed_outlines' => $course->outlines->where('is_completed', true)->count(),
            'total_quizzes' => $course->outlines->where('has_quiz', true)->count(),
            'total_content_items' => $course->outlines->sum(function ($outline) {
                return $outline->contents->count();
            }),
            'total_study_time' => $course->progressTracking->sum('time_spent_minutes'),
        ];

        return Inertia::render('Admin/Courses/Show', [
            'course' => $course,
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        $students = StudentProfile::with('user')
            ->get()
            ->map(function ($profile) {
                return [
                    'id' => $profile->id,
                    'name' => $profile->user->name,
                    'email' => $profile->user->email,
                    'current_level' => $profile->current_level,
                ];
            });

        $examBoards = ExamBoard::active()->get();

        return Inertia::render('Admin/Courses/Create', [
            'students' => $students,
            'exam_boards' => $examBoards,
            'levels' => ['beginner', 'intermediate', 'advanced', 'expert'],
        ]);
    }

    public function store(Request $request, CourseGenerationService $courseService)
    {
        $request->validate([
            'student_profile_id' => 'required|exists:student_profiles,id',
            'exam_board_id' => 'nullable|exists:exam_boards,id',
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'string',
        ]);

        $studentProfile = StudentProfile::findOrFail($request->student_profile_id);

        try {
            $course = $courseService->generateCourse($studentProfile, [
                'title' => $request->title,
                'subject' => $request->subject,
                'description' => $request->description,
                'exam_board_id' => $request->exam_board_id,
                'learning_objectives' => $request->learning_objectives,
            ]);

            return redirect()->route('admin.courses.show', $course->id)
                ->with('success', 'Course generated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to generate course: ' . $e->getMessage());
        }
    }

    public function edit(Course $course)
    {
        $course->load(['studentProfile.user', 'examBoard']);

        $examBoards = ExamBoard::active()->get();

        return Inertia::render('Admin/Courses/Edit', [
            'course' => $course,
            'exam_boards' => $examBoards,
            'levels' => ['beginner', 'intermediate', 'advanced', 'expert'],
            'statuses' => ['draft', 'active', 'completed', 'paused'],
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced,expert',
            'status' => 'required|in:draft,active,completed,paused',
            'exam_board_id' => 'nullable|exists:exam_boards,id',
            'target_completion_date' => 'required|date|after:today',
        ]);

        $course->update([
            'title' => $request->title,
            'subject' => $request->subject,
            'description' => $request->description,
            'level' => $request->level,
            'status' => $request->status,
            'exam_board_id' => $request->exam_board_id,
            'target_completion_date' => $request->target_completion_date,
        ]);

        return redirect()->route('admin.courses.show', $course->id)
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    public function updateProgress(Course $course)
    {
        $course->updateProgress();

        return redirect()->back()->with('success', 'Course progress updated successfully.');
    }

    public function regenerateOutline(Course $course, CourseGenerationService $courseService)
    {
        try {
            // Delete existing outlines
            $course->outlines()->delete();

            // Regenerate outline
            $studentProfile = $course->studentProfile;
            $courseService->generateCourseOutline($course, $studentProfile);

            return redirect()->back()->with('success', 'Course outline regenerated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to regenerate outline: ' . $e->getMessage());
        }
    }

    public function getCourseAnalytics(Course $course)
    {
        $analytics = [
            'progress_over_time' => $this->getProgressOverTime($course),
            'quiz_performance' => $this->getQuizPerformance($course),
            'engagement_metrics' => $this->getEngagementMetrics($course),
        ];

        return response()->json($analytics);
    }

    private function getProgressOverTime(Course $course)
    {
        return $course->progressTracking()
            ->selectRaw('DATE(created_at) as date, SUM(time_spent_minutes) as study_time')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getQuizPerformance(Course $course)
    {
        return $course->outlines()
            ->whereHas('quiz.attempts')
            ->with(['quiz.attempts' => function ($query) {
                $query->selectRaw('quiz_id, AVG(percentage) as avg_score, COUNT(*) as attempt_count')
                    ->groupBy('quiz_id');
            }])
            ->get()
            ->map(function ($outline) {
                return [
                    'topic' => $outline->title,
                    'avg_score' => $outline->quiz->attempts->first()->avg_score ?? 0,
                    'attempt_count' => $outline->quiz->attempts->first()->attempt_count ?? 0,
                ];
            });
    }

    private function getEngagementMetrics(Course $course)
    {
        return [
            'total_study_time' => $course->progressTracking()->sum('time_spent_minutes'),
            'chat_sessions_count' => $course->chatSessions()->count(),
            'messages_count' => $course->chatSessions()->withCount('messages')->get()->sum('messages_count'),
            'recent_activity' => $course->progressTracking()
                ->with('user')
                ->latest()
                ->limit(10)
                ->get(),
        ];
    }


}
