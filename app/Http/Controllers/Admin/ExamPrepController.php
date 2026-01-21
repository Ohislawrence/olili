<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamPrep;
use App\Models\ExamBoard;
use App\Models\Subject;
use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ExamPrepController extends Controller
{
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
                'mixed' => 'Mixed'
            ],
            'questionTypes' => [
                'multiple_choice' => 'Multiple Choice',
                'true_false' => 'True/False',
                'short_answer' => 'Short Answer',
                'multiple_answer' => 'Multiple Answer',
                'mixed' => 'Mixed'
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
            'total_questions' => 'required|integer|min:10|max:200',
            'time_limit_minutes' => 'required|integer|min:10|max:300',
            'passing_score' => 'required|integer|min:1|max:100',
            'max_attempts' => 'required|integer|min:0',
            'randomize_questions' => 'boolean',
            'allow_pause' => 'boolean',
            'show_results_immediately' => 'boolean',
            'is_public' => 'boolean',
            'question_criteria' => 'nullable|array',
            'question_distribution' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Generate slug
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;

            // Ensure unique slug
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
                'question_criteria' => $validated['question_criteria'] ?? null,
                'question_distribution' => $validated['question_distribution'] ?? null,
                'status' => 'draft',
            ]);

            // Generate questions from quiz pool
            $this->generateQuestionsFromPool($examPrep);

            DB::commit();

            return redirect()->route('admin.exam-preps.edit', $examPrep)
                ->with('success', 'Exam prep created successfully!');

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
                $query->orderBy('difficulty')->orderBy('id');
            },
            'attempts' => function ($query) {
                $query->latest()->limit(10)->with('user');
            }
        ]);

        return Inertia::render('Admin/ExamPreps/Show', [
            'examPrep' => $examPrep,
            'statistics' => [
                'total_attempts' => $examPrep->attempts()->count(),
                'average_score' => $examPrep->attempts()->avg('percentage') ?? 0,
                'pass_rate' => $examPrep->attempts()->where('is_passed', true)->count() / max($examPrep->attempts()->count(), 1) * 100,
                'completion_rate' => $examPrep->attempts()->whereNotNull('completed_at')->count() / max($examPrep->attempts()->count(), 1) * 100,
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
                'mixed' => 'Mixed'
            ],
            'questionTypes' => [
                'multiple_choice' => 'Multiple Choice',
                'true_false' => 'True/False',
                'short_answer' => 'Short Answer',
                'multiple_answer' => 'Multiple Answer',
                'mixed' => 'Mixed'
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
            'total_questions' => 'required|integer|min:10|max:200',
            'time_limit_minutes' => 'required|integer|min:10|max:300',
            'passing_score' => 'required|integer|min:1|max:100',
            'max_attempts' => 'required|integer|min:0',
            'randomize_questions' => 'boolean',
            'allow_pause' => 'boolean',
            'show_results_immediately' => 'boolean',
            'is_public' => 'boolean',
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

            // Regenerate questions if criteria changed
            if ($request->has('regenerate_questions') && $request->regenerate_questions) {
                $this->generateQuestionsFromPool($examPrep);
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
     * Generate questions from quiz pool.
     */
    private function generateQuestionsFromPool(ExamPrep $examPrep)
    {
        // Clear existing questions
        $examPrep->questions()->delete();

        // Get quizzes based on criteria
        $query = Quiz::whereHas('courseOutline.module.course', function ($q) use ($examPrep) {
            $q->where('exam_board_id', $examPrep->exam_board_id);

            if ($examPrep->subject_id) {
                $q->where('subject_id', $examPrep->subject_id);
            }

            if ($examPrep->course_id) {
                $q->where('id', $examPrep->course_id);
            }
        })->where('is_active', true);

        // Apply question criteria
        if ($examPrep->question_criteria) {
            foreach ($examPrep->question_criteria as $criterion => $value) {
                if ($value && $value !== 'mixed') {
                    $query->whereJsonContains('questions', [
                        [$criterion => $value]
                    ]);
                }
            }
        }

        $quizzes = $query->get();
        $allQuestions = [];

        // Extract questions from quizzes
        foreach ($quizzes as $quiz) {
            foreach ($quiz->questions as $index => $question) {
                $allQuestions[] = [
                    'exam_prep_id' => $examPrep->id,
                    'quiz_id' => $quiz->id,
                    'course_outline_id' => $quiz->course_outline_id,
                    'question_text' => $question['question'],
                    'options' => $question['options'] ?? [],
                    'correct_answer' => $question['correct_answer'],
                    'question_type' => $question['type'] ?? 'multiple_choice',
                    'points' => $question['points'] ?? 1,
                    'difficulty' => $question['difficulty'] ?? 'medium',
                    'metadata' => $question['metadata'] ?? [],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Shuffle if needed
        if ($examPrep->randomize_questions) {
            shuffle($allQuestions);
        }

        // Apply distribution if specified
        $selectedQuestions = [];

        if ($examPrep->question_distribution) {
            $distribution = [];
            foreach ($examPrep->question_distribution as $difficulty => $count) {
                $filtered = array_filter($allQuestions, fn($q) => $q['difficulty'] === $difficulty);
                $selected = array_slice($filtered, 0, min($count, count($filtered)));
                $distribution = array_merge($distribution, $selected);
            }
            $selectedQuestions = $distribution;
        } else {
            $selectedQuestions = $allQuestions;
        }

        // Take the required number of questions
        $selectedQuestions = array_slice($selectedQuestions, 0, $examPrep->total_questions);

        // Insert questions
        if (!empty($selectedQuestions)) {
            DB::table('exam_prep_questions')->insert($selectedQuestions);
        }

        return count($selectedQuestions);
    }

    /**
     * API endpoint to generate questions.
     */
    public function generateQuestions(ExamPrep $examPrep)
    {
        try {
            $count = $this->generateQuestionsFromPool($examPrep);

            return response()->json([
                'success' => true,
                'message' => "Generated {$count} questions for exam prep.",
                'question_count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate questions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Publish the exam prep.
     */
    public function publish(ExamPrep $examPrep)
    {
        try {
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
     * Get exam prep statistics.

    public function statistics(ExamPrep $examPrep)
    {
        $attempts = $examPrep->attempts()->completed()->get();
        $totalAttempts = $attempts->count();

        $statistics = [
            'total_attempts' => $totalAttempts,
            'average_score' => $attempts->avg('percentage') ?? 0,
            'pass_rate' => $attempts->where('is_passed', true)->count() / max($totalAttempts, 1) * 100,
            'completion_rate' => $attempts->count() / max($examPrep->enrolled_count, 1) * 100,
            'average_time' => $attempts->avg('time_spent_seconds') ?? 0,
            'question_stats' => $this->getQuestionStatistics($examPrep)
        ];

        return response()->json($statistics);
    }
*/
    /**
     * Get question-level statistics.
     */
    private function getQuestionStatistics(ExamPrep $examPrep)
    {
        return $examPrep->questions()
            ->select(
                'difficulty',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(times_correct) as total_correct'),
                DB::raw('SUM(times_incorrect) as total_incorrect')
            )
            ->groupBy('difficulty')
            ->get()
            ->map(function ($stat) {
                $totalAttempts = $stat->total_correct + $stat->total_incorrect;
                return [
                    'difficulty' => $stat->difficulty,
                    'total' => $stat->total,
                    'success_rate' => $totalAttempts > 0 ?
                        ($stat->total_correct / $totalAttempts) * 100 : 0
                ];
            });
    }

    /**
     * Get exam prep attempts.
     */
    public function attempts(ExamPrep $examPrep)
    {
        $attempts = $examPrep->attempts()
            ->with('user')
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/ExamPreps/Attempts', [
            'examPrep' => $examPrep,
            'attempts' => $attempts
        ]);
    }

    /**
 * Get enhanced statistics for exam prep.
 */
public function statistics(ExamPrep $examPrep, Request $request)
{
    $timeRange = $request->get('time_range', '30days');

    // Calculate date range
    $dateRange = $this->getDateRange($timeRange);

    $statistics = [
        'total_attempts' => $examPrep->attempts()->count(),
        'unique_students' => $examPrep->attempts()->distinct('user_id')->count(),
        'passed_attempts' => $examPrep->attempts()->where('is_passed', true)->count(),
        'average_score' => $examPrep->attempts()->avg('percentage') ?? 0,
        'pass_rate' => $examPrep->attempts()->count() > 0 ?
            ($examPrep->attempts()->where('is_passed', true)->count() / $examPrep->attempts()->count()) * 100 : 0,
        'completion_rate' => $examPrep->attempts()->count() / max($examPrep->enrolled_count, 1) * 100,
        'average_time' => $examPrep->attempts()->avg('time_spent_seconds') ?? 0,
        'question_stats' => $this->getQuestionStatistics($examPrep),
        'performance_over_time' => $this->getPerformanceOverTime($examPrep, $dateRange),
        'attempt_distribution' => $this->getAttemptDistribution($examPrep, $dateRange),
        'score_distribution' => $this->getScoreDistribution($examPrep),
    ];

    // Recent attempts (last 10)
    $recentAttempts = $examPrep->attempts()
        ->with('user')
        ->latest('completed_at')
        ->limit(10)
        ->get()
        ->map(function ($attempt) {
            return [
                'id' => $attempt->id,
                'user' => [
                    'id' => $attempt->user->id,
                    'name' => $attempt->user->name,
                    'email' => $attempt->user->email,
                ],
                'score' => $attempt->score,
                'total_points' => $attempt->quiz?->getTotalPoints() ?? 0,
                'percentage' => $attempt->percentage,
                'time_spent_seconds' => $attempt->time_spent_seconds,
                'is_passed' => $attempt->is_passed,
                'completed_at' => $attempt->completed_at,
            ];
        });

    // Top performers
    $topPerformers = $examPrep->attempts()
        ->select('user_id', DB::raw('MAX(percentage) as best_score'), DB::raw('COUNT(*) as attempt_count'))
        ->with('user')
        ->groupBy('user_id')
        ->orderBy('best_score', 'desc')
        ->limit(5)
        ->get()
        ->map(function ($attempt) {
            return [
                'id' => $attempt->user_id,
                'name' => $attempt->user->name,
                'email' => $attempt->user->email,
                'best_score' => $attempt->best_score,
                'attempt_count' => $attempt->attempt_count,
            ];
        });

    // Weak areas
    $weakAreas = $this->getWeakAreas($examPrep);

    return response()->json([
        'statistics' => $statistics,
        'recent_attempts' => $recentAttempts,
        'top_performers' => $topPerformers,
        'weak_areas' => $weakAreas,
    ]);
}

/**
 * Get date range based on time range.
 */
private function getDateRange($timeRange)
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
            $startDate = now()->subYears(10); // 10 years back
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
private function getPerformanceOverTime(ExamPrep $examPrep, $dateRange)
{
    [$startDate, $endDate] = $dateRange;

    $attempts = $examPrep->attempts()
        ->whereBetween('completed_at', [$startDate, $endDate])
        ->orderBy('completed_at')
        ->get(['completed_at', 'percentage']);

    // Group by day
    $grouped = $attempts->groupBy(function ($attempt) {
        return $attempt->completed_at->format('Y-m-d');
    });

    $labels = [];
    $scores = [];

    $currentDate = $startDate->copy();
    while ($currentDate <= $endDate) {
        $dateKey = $currentDate->format('Y-m-d');
        $labels[] = $currentDate->format('M d');

        if (isset($grouped[$dateKey])) {
            $scores[] = $grouped[$dateKey]->avg('percentage') ?? 0;
        } else {
            $scores[] = 0;
        }

        $currentDate->addDay();
    }

    return [
        'labels' => $labels,
        'scores' => $scores,
    ];
}

/**
 * Get attempt distribution by day of week.
 */
private function getAttemptDistribution(ExamPrep $examPrep, $dateRange)
{
    [$startDate, $endDate] = $dateRange;

    $attempts = $examPrep->attempts()
        ->whereBetween('completed_at', [$startDate, $endDate])
        ->selectRaw('DAYOFWEEK(completed_at) as day, COUNT(*) as count')
        ->groupBy('day')
        ->orderBy('day')
        ->get();

    $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $counts = array_fill(0, 7, 0);

    foreach ($attempts as $attempt) {
        $dayIndex = ($attempt->day - 1) % 7;
        $counts[$dayIndex] = $attempt->count;
    }

    return [
        'labels' => $daysOfWeek,
        'counts' => $counts,
    ];
}

/**
 * Get score distribution in buckets.
 */
private function getScoreDistribution(ExamPrep $examPrep)
{
    $attempts = $examPrep->attempts()->get(['percentage']);

    $buckets = [0, 0, 0, 0]; // 0-50, 51-70, 71-85, 86-100

    foreach ($attempts as $attempt) {
        $percentage = $attempt->percentage;

        if ($percentage <= 50) {
            $buckets[0]++;
        } elseif ($percentage <= 70) {
            $buckets[1]++;
        } elseif ($percentage <= 85) {
            $buckets[2]++;
        } else {
            $buckets[3]++;
        }
    }

    return $buckets;
}

/**
 * Get weak areas (questions with low success rates).
 */
private function getWeakAreas(ExamPrep $examPrep)
{
    $questions = $examPrep->questions()
        ->select('id', 'question_text', 'difficulty', 'metadata', 'times_correct', 'times_incorrect')
        ->whereRaw('(times_correct + times_incorrect) > 0')
        ->orderByRaw('times_correct / (times_correct + times_incorrect) ASC')
        ->limit(5)
        ->get();

    return $questions->map(function ($question) {
        $totalAttempts = $question->times_correct + $question->times_incorrect;
        $successRate = $totalAttempts > 0 ? ($question->times_correct / $totalAttempts) * 100 : 0;

        // Extract topic from metadata
        $topic = $question->metadata['topic'] ?? $question->metadata['module'] ?? 'Unknown Topic';

        // Get common wrong answers from metadata
        $commonMistakes = $question->metadata['common_mistakes'] ?? [];

        return [
            'question_id' => $question->id,
            'question_text' => substr($question->question_text, 0, 100) . '...',
            'topic' => $topic,
            'difficulty' => $question->difficulty,
            'success_rate' => $successRate,
            'correct_count' => $question->times_correct,
            'incorrect_count' => $question->times_incorrect,
            'common_mistakes' => array_slice($commonMistakes, 0, 3), // Limit to 3
        ];
    });
}
}
