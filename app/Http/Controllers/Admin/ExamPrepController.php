<?php
// app/Http/Controllers/Admin/ExamPrepController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamPrep;
use App\Models\ExamBoard;
use App\Models\Subject;
use App\Models\Course;
use App\Services\ExamPrepGenerationService;
use App\Jobs\GenerateExamPrepQuestions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ExamPrepController extends Controller
{
    protected $generationService;

    public function __construct(ExamPrepGenerationService $generationService)
    {
        $this->generationService = $generationService;
    }

    /**
     * Display a listing of exam preps.
     */
    public function index(Request $request)
    {
        $query = ExamPrep::with(['examBoard', 'subject', 'course'])
            ->latest();

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('exam_board_id')) {
            $query->where('exam_board_id', $request->exam_board_id);
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $examPreps = $query->paginate(20);

        return Inertia::render('Admin/ExamPreps/Index', [
            'examPreps' => $examPreps,
            'filters' => $request->only(['search', 'status', 'exam_board_id', 'subject_id']),
            'examBoards' => ExamBoard::active()->get(),
            'subjects' => Subject::all(),
            'statuses' => [
                'draft' => 'Draft',
                'active' => 'Active',
                'archived' => 'Archived'
            ]
        ]);
    }

    /**
     * Show the form for creating a new exam prep.
     */
    public function create()
    {
        return Inertia::render('Admin/ExamPreps/Create', [
            'examBoards' => ExamBoard::active()->get(),
            'subjects' => Subject::all(),
            'courses' => Course::where('status', 'active')->get(),
            'difficultyLevels' => [
                'easy' => 'Easy',
                'medium' => 'Medium',
                'hard' => 'Hard',
            ],
            'aiModels' => [
                'gpt-4' => 'GPT-4',
                'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
                'claude-2' => 'Claude 2',
                'llama2' => 'Llama 2',
            ]
        ]);
    }

    /**
     * Store a newly created exam prep in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_board_id' => 'required|exists:exam_boards,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'course_id' => 'nullable|exists:courses,id',
            'total_questions' => 'required|integer|min:5|max:100',
            'time_limit_minutes' => 'required|integer|min:10|max:300',
            'passing_score' => 'required|integer|min:1|max:100',
            'max_attempts' => 'required|integer|min:0',
            'randomize_questions' => 'boolean',
            'allow_pause' => 'boolean',
            'show_results_immediately' => 'boolean',
            'is_public' => 'boolean',
            'status' => 'nullable|in:draft,active,archived',
            'question_criteria' => 'nullable|array',
            'question_distribution' => 'nullable|array',
            'ai_model' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Generate slug
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;

            while (ExamPrep::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Create exam prep
            $examPrep = ExamPrep::create([
                'slug' => $slug,
                'name' => $validated['name'],
                'description' => $validated['description'],
                'exam_board_id' => $validated['exam_board_id'],
                'subject_id' => $validated['subject_id'],
                'course_id' => $validated['course_id'],
                'total_questions' => $validated['total_questions'],
                'time_limit_minutes' => $validated['time_limit_minutes'],
                'passing_score' => $validated['passing_score'],
                'max_attempts' => $validated['max_attempts'],
                'randomize_questions' => $validated['randomize_questions'] ?? true,
                'allow_pause' => $validated['allow_pause'] ?? false,
                'show_results_immediately' => $validated['show_results_immediately'] ?? true,
                'is_public' => $validated['is_public'] ?? false,
                'status' => $validated['status'] ?? 'draft',
                'question_criteria' => $validated['question_criteria'] ?? null,
                'question_distribution' => $validated['question_distribution'] ?? null,
                'content_generation_status' => 'pending',
                'generation_parameters' => [
                    'ai_model' => $validated['ai_model'] ?? 'gpt-4',
                    'generated_at' => now()->toDateTimeString(),
                ],
            ]);

            DB::commit();

            // Dispatch job to generate questions
            if ($examPrep->status === 'active' || $request->input('generate_now', false)) {
                GenerateExamPrepQuestions::dispatch($examPrep);
            }

            return redirect()->route('admin.exam-preps.show', $examPrep)
                ->with('success', 'Exam prep created successfully! Questions are being generated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create exam prep: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified exam prep.
     */
    public function show(ExamPrep $examPrep)
    {
        $examPrep->load([
            'examBoard',
            'subject',
            'course',
            'questions' => function ($query) {
                $query->orderBy('order');
            },
            'attempts' => function ($query) {
                $query->latest()->limit(10)->with('user');
            }
        ]);

        // Calculate statistics
        $statistics = [
            'total_attempts' => $examPrep->attempts()->count(),
            'unique_students' => $examPrep->attempts()->distinct('user_id')->count(),
            'average_score' => round($examPrep->attempts()->avg('percentage') ?? 0, 1),
            'pass_rate' => $this->calculatePassRate($examPrep),
            'completion_rate' => $this->calculateCompletionRate($examPrep),
            'average_time' => round($examPrep->attempts()->avg('time_spent_seconds') ?? 0),
            'difficulty_distribution' => $examPrep->getDifficultyDistribution(),
        ];

        return Inertia::render('Admin/ExamPreps/Show', [
            'examPrep' => $examPrep,
            'statistics' => $statistics,
            'generationStatus' => [
                'status' => $examPrep->content_generation_status,
                'started_at' => $examPrep->content_generation_started_at,
                'completed_at' => $examPrep->content_generation_completed_at,
                'summary' => $examPrep->generation_summary,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified exam prep.
     */
    public function edit(ExamPrep $examPrep)
    {
        $examPrep->load(['questions']);

        return Inertia::render('Admin/ExamPreps/Edit', [
            'examPrep' => $examPrep,
            'examBoards' => ExamBoard::active()->get(),
            'subjects' => Subject::all(),
            'courses' => Course::where('status', 'active')->get(),
            'difficultyLevels' => [
                'easy' => 'Easy',
                'medium' => 'Medium',
                'hard' => 'Hard',
            ],
            'aiModels' => [
                'gpt-4' => 'GPT-4',
                'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
                'claude-2' => 'Claude 2',
                'llama2' => 'Llama 2',
            ]
        ]);
    }

    /**
     * Update the specified exam prep in storage.
     */
    public function update(Request $request, ExamPrep $examPrep)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_board_id' => 'required|exists:exam_boards,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'course_id' => 'nullable|exists:courses,id',
            'total_questions' => 'required|integer|min:5|max:100',
            'time_limit_minutes' => 'required|integer|min:10|max:300',
            'passing_score' => 'required|integer|min:1|max:100',
            'max_attempts' => 'required|integer|min:0',
            'randomize_questions' => 'boolean',
            'allow_pause' => 'boolean',
            'show_results_immediately' => 'boolean',
            'is_public' => 'boolean',
            'status' => 'required|in:draft,active,archived',
            'question_criteria' => 'nullable|array',
            'question_distribution' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Update slug if name changed
            if ($examPrep->name !== $validated['name']) {
                $slug = Str::slug($validated['name']);
                $originalSlug = $slug;
                $counter = 1;

                while (ExamPrep::where('slug', $slug)->where('id', '!=', $examPrep->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                $examPrep->slug = $slug;
            }

            $examPrep->update($validated);

            // Regenerate questions if criteria changed and requested
            if ($request->has('regenerate_questions') && $request->regenerate_questions) {
                $examPrep->update(['content_generation_status' => 'pending']);
                GenerateExamPrepQuestions::dispatch($examPrep);
            }

            DB::commit();

            return back()->with('success', 'Exam prep updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update exam prep: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified exam prep from storage.
     */
    public function destroy(ExamPrep $examPrep)
    {
        try {
            $examPrep->delete();
            return redirect()->route('admin.exam-preps.index')
                ->with('success', 'Exam prep deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete exam prep: ' . $e->getMessage());
        }
    }

    /**
     * Generate questions using AI.
     */
    public function generateQuestions(ExamPrep $examPrep)
    {
        try {
            GenerateExamPrepQuestions::dispatch($examPrep);

            return response()->json([
                'success' => true,
                'message' => 'Question generation started. This may take a few moments.',
                'status' => 'processing'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start question generation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get generation status.
     */
    public function generationStatus(ExamPrep $examPrep)
    {
        return response()->json([
            'status' => $examPrep->content_generation_status,
            'started_at' => $examPrep->content_generation_started_at,
            'completed_at' => $examPrep->content_generation_completed_at,
            'summary' => $examPrep->generation_summary,
            'question_count' => $examPrep->questions()->count(),
        ]);
    }

    /**
     * Publish the exam prep.
     */
    public function publish(ExamPrep $examPrep)
    {
        try {
            // Check if questions exist
            if ($examPrep->questions()->count() === 0) {
                // Trigger generation if no questions
                $examPrep->update(['content_generation_status' => 'pending']);
                GenerateExamPrepQuestions::dispatch($examPrep);

                return back()->with('info', 'Question generation started. Exam will be published once generation is complete.');
            }

            $examPrep->update([
                'status' => 'active',
                'is_public' => true
            ]);

            return back()->with('success', 'Exam prep published successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to publish exam prep: ' . $e->getMessage());
        }
    }

    /**
     * Archive the exam prep.
     */
    public function archive(ExamPrep $examPrep)
    {
        try {
            $examPrep->update(['status' => 'archived']);
            return back()->with('success', 'Exam prep archived successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to archive exam prep: ' . $e->getMessage());
        }
    }

    /**
     * Get enhanced statistics for exam prep.
     */
    public function statistics(ExamPrep $examPrep, Request $request)
    {
        $timeRange = $request->get('time_range', '30days');
        $dateRange = $this->getDateRange($timeRange);

        $statistics = [
            'total_attempts' => $examPrep->attempts()->count(),
            'unique_students' => $examPrep->attempts()->distinct('user_id')->count(),
            'passed_attempts' => $examPrep->attempts()->where('is_passed', true)->count(),
            'average_score' => round($examPrep->attempts()->avg('percentage') ?? 0, 1),
            'pass_rate' => $this->calculatePassRate($examPrep),
            'completion_rate' => $this->calculateCompletionRate($examPrep),
            'average_time' => round($examPrep->attempts()->avg('time_spent_seconds') ?? 0),
            'difficulty_distribution' => $examPrep->getDifficultyDistribution(),
            'type_distribution' => $examPrep->getQuestionTypeDistribution(),
            'performance_over_time' => $this->getPerformanceOverTime($examPrep, $dateRange),
            'score_distribution' => $this->getScoreDistribution($examPrep),
        ];

        $recentAttempts = $examPrep->attempts()
            ->with('user')
            ->latest('completed_at')
            ->limit(10)
            ->get()
            ->map(fn($attempt) => [
                'id' => $attempt->id,
                'user' => [
                    'id' => $attempt->user->id,
                    'name' => $attempt->user->name,
                    'email' => $attempt->user->email,
                ],
                'score' => $attempt->score,
                'percentage' => $attempt->percentage,
                'time_spent_seconds' => $attempt->time_spent_seconds,
                'is_passed' => $attempt->is_passed,
                'completed_at' => $attempt->completed_at,
            ]);

        $topPerformers = $examPrep->attempts()
            ->select('user_id', DB::raw('MAX(percentage) as best_score'), DB::raw('COUNT(*) as attempt_count'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('best_score', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($attempt) => [
                'id' => $attempt->user_id,
                'name' => $attempt->user->name,
                'email' => $attempt->user->email,
                'best_score' => round($attempt->best_score, 1),
                'attempt_count' => $attempt->attempt_count,
            ]);

        $weakAreas = $this->getWeakAreas($examPrep);

        return response()->json([
            'statistics' => $statistics,
            'recent_attempts' => $recentAttempts,
            'top_performers' => $topPerformers,
            'weak_areas' => $weakAreas,
        ]);
    }

    /**
     * Calculate pass rate
     */
    protected function calculatePassRate(ExamPrep $examPrep): float
    {
        $total = $examPrep->attempts()->count();
        if ($total === 0) return 0;

        $passed = $examPrep->attempts()->where('is_passed', true)->count();
        return round(($passed / $total) * 100, 1);
    }

    /**
     * Calculate completion rate
     */
    protected function calculateCompletionRate(ExamPrep $examPrep): float
    {
        $enrolled = $examPrep->enrolled_count ?? 0;
        if ($enrolled === 0) return 0;

        $completed = $examPrep->attempts()->count();
        return $enrolled > 0 ? round(($completed / $enrolled) * 100, 1) : 0;
    }

    /**
     * Get date range based on time range.
     */
    private function getDateRange($timeRange): array
    {
        $endDate = now();

        switch ($timeRange) {
            case '7days':
                $startDate = now()->subDays(7);
                break;
            case '90days':
                $startDate = now()->subDays(90);
                break;
            case 'all':
                $startDate = now()->subYears(10);
                break;
            case '30days':
            default:
                $startDate = now()->subDays(30);
                break;
        }

        return [$startDate, $endDate];
    }

    /**
     * Get performance over time data.
     */
    private function getPerformanceOverTime(ExamPrep $examPrep, array $dateRange): array
    {
        [$startDate, $endDate] = $dateRange;

        $attempts = $examPrep->attempts()
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->orderBy('completed_at')
            ->get(['completed_at', 'percentage']);

        $grouped = $attempts->groupBy(fn($attempt) => $attempt->completed_at->format('Y-m-d'));

        $labels = [];
        $scores = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('M d');
            $scores[] = isset($grouped[$dateKey]) ? round($grouped[$dateKey]->avg('percentage'), 1) : 0;
            $currentDate->addDay();
        }

        return ['labels' => $labels, 'scores' => $scores];
    }

    /**
     * Get score distribution in buckets.
     */
    private function getScoreDistribution(ExamPrep $examPrep): array
    {
        $buckets = [0, 0, 0, 0, 0]; // 0-20, 21-40, 41-60, 61-80, 81-100

        $attempts = $examPrep->attempts()->get(['percentage']);

        foreach ($attempts as $attempt) {
            $percentage = $attempt->percentage;

            if ($percentage <= 20) $buckets[0]++;
            elseif ($percentage <= 40) $buckets[1]++;
            elseif ($percentage <= 60) $buckets[2]++;
            elseif ($percentage <= 80) $buckets[3]++;
            else $buckets[4]++;
        }

        return $buckets;
    }

    /**
     * Get weak areas (questions with low success rates).
     */
    private function getWeakAreas(ExamPrep $examPrep): array
    {
        $questions = $examPrep->questions()
            ->select('id', 'question_text', 'difficulty', 'metadata')
            ->withCount(['attempts as total_attempts', 'attempts as correct_count' => function ($query) {
                $query->where('is_correct', true);
            }])
            ->having('total_attempts', '>', 0)
            ->orderByRaw('(correct_count / total_attempts) ASC')
            ->limit(5)
            ->get();

        return $questions->map(function ($question) {
            $successRate = $question->total_attempts > 0
                ? round(($question->correct_count / $question->total_attempts) * 100, 1)
                : 0;

            return [
                'question_id' => $question->id,
                'question_text' => Str::limit($question->question_text, 100),
                'topic' => $question->metadata['topic'] ?? 'Unknown',
                'difficulty' => $question->difficulty,
                'success_rate' => $successRate,
                'total_attempts' => $question->total_attempts,
                'correct_count' => $question->correct_count,
            ];
        })->toArray();
    }
}
