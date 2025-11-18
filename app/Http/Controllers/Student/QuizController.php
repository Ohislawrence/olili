<?php
// app/Http/Controllers/Student/QuizController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\CourseOutline;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user();

        $query = QuizAttempt::with(['quiz.courseOutline.module.course'])
            ->where('user_id', $student->id);

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'completed') {
                $query->whereNotNull('completed_at');
            } elseif ($request->status === 'in_progress') {
                $query->whereNull('completed_at')->whereNotNull('started_at');
            } elseif ($request->status === 'not_started') {
                $query->whereNull('started_at');
            }
        }

        $quizAttempts = $query->latest()
            ->paginate(15);

        $stats = [
            'total_attempts' => $student->quizAttempts()->count(),
            'average_score' => $student->quizAttempts()->whereNotNull('percentage')->avg('percentage'),
            'passed_quizzes' => $student->quizAttempts()->where('is_passed', true)->count(),
            'perfect_scores' => $student->quizAttempts()->where('percentage', 100)->count(),
        ];

        return Inertia::render('Student/Quizzes/Index', [
            'quiz_attempts' => $quizAttempts,
            'stats' => $stats,
            'filters' => $request->only(['status']),
        ]);
    }

    public function show(Quiz $quiz)
    {
        $student = auth()->user();

        // Load the full hierarchy for authorization and context
        $quiz->load(['courseOutline.module.course']);

        // Check if user can access this quiz
        //$this->authorize('view', $quiz->courseOutline->module->course);

        $previousAttempts = QuizAttempt::where('user_id', $student->id)
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->orderBy('attempt_number', 'desc')
            ->get();

        $canAttempt = $quiz->canUserAttempt($student);
        $bestAttempt = $quiz->getUserBestAttempt($student);

        return Inertia::render('Student/Quizzes/Show', [
            'quiz' => $quiz,
            'previous_attempts' => $previousAttempts,
            'can_attempt' => $canAttempt,
            'best_attempt' => $bestAttempt,
            'next_attempt_number' => $previousAttempts->count() + 1,
            'module_context' => [
                'module_title' => $quiz->courseOutline->module->title,
                'course_title' => $quiz->courseOutline->module->course->title,
            ],
        ]);
    }

    public function startAttempt(Quiz $quiz)
    {
        $student = auth()->user();

        // Load course for authorization
        $quiz->load(['courseOutline.module.course']);
        //$this->authorize('view', $quiz->courseOutline->module->course);

        if (!$quiz->canUserAttempt($student)) {
            return redirect()->back()
                ->with('error', 'You have reached the maximum number of attempts for this quiz.');
        }

        $attempt = QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
        ]);

        $attempt->startAttempt();

        return Inertia::render('Student/Quizzes/Take', [
            'quiz' => $quiz->load(['courseOutline.module.course']),
            'attempt' => $attempt,
            'questions' => $quiz->questions,
            'time_limit' => $quiz->time_limit_minutes,
            'module_context' => [
                'module_title' => $quiz->courseOutline->module->title,
                'topic_title' => $quiz->courseOutline->title,
            ],
        ]);
    }

    public function continueAttempt(QuizAttempt $attempt, Quiz $quiz )
    {
        $student = auth()->user();

        // Load course for authorization
        $quiz->load(['courseOutline.module.course']);
        //$this->authorize('view', $quiz->courseOutline->module->course);



        return Inertia::render('Student/Quizzes/Take', [
            'quiz' => $quiz->load(['courseOutline.module.course']),
            'attempt' => $attempt,
            'questions' => $quiz->questions,
            'time_limit' => $quiz->time_limit_minutes,
            'module_context' => [
                'module_title' => $quiz->courseOutline->module->title,
                'topic_title' => $quiz->courseOutline->title,
            ],
        ]);
    }

    public function submitAttempt(QuizAttempt $attempt, Request $request)
    {
        //$this->authorize('update', $attempt);

        $request->validate([
            'answers' => 'required|array',
        ]);

        $quiz = $attempt->quiz;

        // Load the full hierarchy for progress tracking
        $quiz->load(['courseOutline.module.course']);

        // Calculate score
        $scoreData = $quiz->calculateScore($request->answers);

        // Complete the attempt
        $attempt->completeAttempt($request->answers, $scoreData);

        // Record progress
        if ($attempt->quiz->courseOutline) {
            $progressService = app(\App\Services\ProgressTrackingService::class);
            $progressService->recordActivity(
                $attempt->user,
                $quiz->courseOutline->module->course,
                $quiz->courseOutline->id,
                'quiz_attempt',
                $attempt->time_taken_seconds / 60, // Convert to minutes
                true,
                $attempt->percentage
            );

            // If quiz was passed and it's associated with a topic, mark topic as completed
            if ($attempt->is_passed && $quiz->courseOutline) {
                $topic = $quiz->courseOutline;
                if (!$topic->is_completed) {
                    $topic->markAsCompleted();

                    // Update module completion status
                    $progressService->updateModuleCompletion($topic->module);
                }
            }
        }

        return Inertia::render('Student/Quizzes/Result', [
            'attempt' => $attempt->load(['quiz.courseOutline.module.course']),
            'score_data' => $scoreData,
            'questions' => $quiz->questions,
            'user_answers' => $request->answers,
            'module_context' => [
                'module_title' => $quiz->courseOutline->module->title,
                'course_title' => $quiz->courseOutline->module->course->title,
            ],
        ]);
    }

    public function showResult(QuizAttempt $attempt)
    {
        // Load necessary relationships including the quiz itself
        $attempt->load(['quiz.courseOutline.module.course']); // Removed '.questions' from the load method

        return Inertia::render('Student/Quizzes/Result', [
            'attempt' => $attempt,
            // Access questions directly as an attribute of the quiz model
            'questions' => $attempt->quiz->questions,
            'user_answers' => $attempt->answers,
            'module_context' => [
                'module_title' => $attempt->quiz->courseOutline->module->title,
                'course_title' => $attempt->quiz->courseOutline->module->course->title,
                'topic_title' => $attempt->quiz->courseOutline->title,
            ],
        ]);
    }

    public function getQuizHistory(Quiz $quiz)
    {
        $student = auth()->user();

        // Load course for authorization
        $quiz->load(['courseOutline.module.course']);
        //$this->authorize('view', $quiz->courseOutline->module->course);

        $attempts = QuizAttempt::where('user_id', $student->id)
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('completed_at')
            ->orderBy('attempt_number', 'desc')
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'attempt_number' => $attempt->attempt_number,
                    'score' => $attempt->score,
                    'percentage' => $attempt->percentage,
                    'is_passed' => $attempt->is_passed,
                    'time_taken' => $attempt->getTimeTakenFormatted(),
                    'completed_at' => $attempt->completed_at->format('M j, Y g:i A'),
                ];
            });

        return response()->json([
            'attempts' => $attempts,
            'quiz' => [
                'title' => $quiz->title,
                'passing_score' => $quiz->passing_score,
                'max_attempts' => $quiz->max_attempts,
                'topic_title' => $quiz->courseOutline->title,
                'module_title' => $quiz->courseOutline->module->title,
            ],
        ]);
    }

    /**
     * Show module-level quizzes (quizzes that cover multiple topics in a module)
     */
    public function showModuleQuizzes($courseId, $moduleId)
    {
        $student = auth()->user();

        $module = \App\Models\Module::with(['course', 'topics.quizzes'])
            ->where('course_id', $courseId)
            ->where('id', $moduleId)
            ->firstOrFail();

        //$this->authorize('view', $module->course);

        // Get all quizzes for this module (both topic-specific and module-level)
        $topicQuizzes = Quiz::whereHas('courseOutline', function ($query) use ($moduleId) {
                $query->where('module_id', $moduleId);
            })
            ->with(['courseOutline'])
            ->get();

        $moduleQuizzes = Quiz::where('module_id', $moduleId)
            ->get();

        $allQuizzes = $topicQuizzes->merge($moduleQuizzes);

        $quizAttempts = QuizAttempt::where('user_id', $student->id)
            ->whereIn('quiz_id', $allQuizzes->pluck('id'))
            ->with(['quiz'])
            ->get()
            ->groupBy('quiz_id');

        return Inertia::render('Student/Quizzes/ModuleQuizzes', [
            'module' => $module,
            'quizzes' => $allQuizzes->map(function ($quiz) use ($quizAttempts) {
                $attempts = $quizAttempts->get($quiz->id, collect());
                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'description' => $quiz->description,
                    'time_limit_minutes' => $quiz->time_limit_minutes,
                    'passing_score' => $quiz->passing_score,
                    'max_attempts' => $quiz->max_attempts,
                    'question_count' => $quiz->question_count,
                    'is_module_quiz' => !is_null($quiz->module_id),
                    'topic_title' => $quiz->courseOutline->title ?? null,
                    'attempts' => $attempts->map(function ($attempt) {
                        return [
                            'attempt_number' => $attempt->attempt_number,
                            'percentage' => $attempt->percentage,
                            'is_passed' => $attempt->is_passed,
                            'completed_at' => $attempt->completed_at?->format('M j, Y g:i A'),
                        ];
                    }),
                    'best_score' => $attempts->max('percentage'),
                    'can_attempt' => $quiz->canUserAttempt(auth()->user()),
                ];
            }),
        ]);
    }

    //in-page methods
    public function start(Quiz $quiz, Request $request)
    {
        $student = auth()->user();
        // Check if user can access this quiz
        if ($quiz->courseOutline->module->course->student_profile_id !== $student->studentProfile->id) {
            return response()->json([
                'error' => 'You do not have access to this quiz.'
            ], 403);
        }

        if (!$quiz->canUserAttempt($student)) {
            return response()->json([
                'error' => 'You cannot attempt this quiz anymore. Maximum attempts reached.'
            ], 403);
        }

        try {
            $attempt = QuizAttempt::create([
                'user_id' => $student->id,
                'quiz_id' => $quiz->id,
            ]);

            $attempt->startAttempt();

            return response()->json([
                'success' => true,
                'attempt' => [
                    'id' => $attempt->id,
                    'quiz_id' => $attempt->quiz_id,
                    'user_id' => $attempt->user_id,
                    'attempt_number' => $attempt->attempt_number ,
                    'started_at' => $attempt->started_at,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Quiz start failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to start quiz: ' . $e->getMessage()
            ], 500);
        }
    }
    public function submit(QuizAttempt $attempt, Request $request)
    {
        $student = auth()->user();

        // Validate the attempt belongs to the user and is not completed
        if ($attempt->user_id !== $student->id) {
            \Log::error('Quiz attempt user mismatch', [
                'attempt_user_id' => $attempt->user_id,
                'current_user_id' => $student->id
            ]);
            return response()->json([
                'error' => 'Invalid attempt'
            ], 403);
        }

        if ($attempt->completed_at) {
            return response()->json([
                'error' => 'This quiz attempt has already been completed'
            ], 400);
        }

        try {
            // Validate the request
            $validated = $request->validate([
                'answers' => 'required|array',
                'answers.*' => 'nullable|string',
            ]);

            $answers = $validated['answers'];

            // Calculate score
            $scoreData = $attempt->quiz->calculateScore($answers);
            // Complete the attempt
            $attempt->completeAttempt($answers, $scoreData);

            // Get detailed results for display
            $detailedResults = $this->getDetailedResults($attempt);

            return response()->json([
                'success' => true,
                'score' => $scoreData['score'],
                'percentage' => $scoreData['percentage'],
                'is_passed' => $scoreData['percentage'] >= $attempt->quiz->passing_score,
                'total_points' => $scoreData['total_points'],
                'detailed_results' => $detailedResults
            ]);

        } catch (\Exception $e) {
            \Log::error('Quiz submission failed', [
                'attempt_id' => $attempt->id,
                'user_id' => $student->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to submit quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getDetailedResults(QuizAttempt $attempt): array
    {
        $detailed = [];
        $userAnswers = $attempt->answers ?? [];
        $questions = $attempt->quiz->questions ?? [];

        foreach ($questions as $index => $question) {
            $userAnswer = $userAnswers[$index] ?? null;
            $isCorrect = $attempt->quiz->isAnswerCorrect(
                $userAnswer,
                $question['correct_answer'],
                $question['type'] ?? 'multiple_choice'
            );

            $detailed[] = [
                'question' => $question['question'],
                'type' => $question['type'] ?? 'multiple_choice',
                'user_answer' => $userAnswer,
                'correct_answer' => $question['correct_answer'],
                'is_correct' => $isCorrect,
                'explanation' => $question['explanation'] ?? '',
                'points' => $question['points'] ?? 1,
                'earned_points' => $isCorrect ? ($question['points'] ?? 1) : 0,
            ];
        }

        return $detailed;
    }
}
