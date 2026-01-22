<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamPrep;
use App\Models\ExamPrepAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamPrepController extends Controller
{
    /**
     * Display a listing of available exam preps.
     */
    public function index(Request $request)
    {
        $query = ExamPrep::with(['examBoard', 'subject'])
            ->where('status', 'active')
            ->where('is_public', true)
            ->latest();

        // Apply filters
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

        $examPreps = $query->paginate(12);

        return Inertia::render('Student/ExamPreps/Index', [
            'examPreps' => $examPreps,
            'filters' => $request->only(['search', 'exam_board_id', 'subject_id']),
            'examBoards' => \App\Models\ExamBoard::active()->get(),
            'subjects' => \App\Models\Subject::all(),
            'myAttempts' => auth()->user()->examPrepAttempts()
                ->with('examPrep')
                ->latest()
                ->limit(5)
                ->get()
        ]);
    }

    /**
     * Display the specified exam prep.
     */
    public function show(ExamPrep $examPrep)
    {
        if (!$examPrep->is_public || $examPrep->status !== 'active') {
            abort(404);
        }

        $user = auth()->user();
        $isEnrolled = $examPrep->enrolledUsers()->where('user_id', $user->id)->exists();
        $attempts = $examPrep->attempts()
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return Inertia::render('Student/ExamPreps/Show', [
            'examPrep' => $examPrep->load(['examBoard', 'subject']),
            'isEnrolled' => $isEnrolled,
            'attempts' => $attempts,
            'canAttempt' => $examPrep->canUserAttempt($user),
            'attemptsCount' => $attempts->count(),
            'bestScore' => $attempts->max('percentage') ?? 0,
        ]);
    }

    /**
     * Enroll student in exam prep.
     */
    public function enroll(ExamPrep $examPrep, Request $request)
    {
        $user = auth()->user();

        if (!$examPrep->canUserAttempt($user)) {
            return back()->with('error', 'Cannot enroll in this exam prep.');
        }

        $enrolled = $examPrep->enrollUser($user);

        if ($enrolled) {
            return redirect()->route('student.exam-preps.instructions', $examPrep)
                ->with('success', 'Successfully enrolled in exam prep!');
        }

        return back()->with('error', 'Already enrolled or cannot enroll.');
    }

    /**
     * Show exam instructions.
     */
    public function instructions(ExamPrep $examPrep)
    {
        $user = auth()->user();

        if (!$examPrep->enrolledUsers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('student.exam-preps.show', $examPrep);
        }

        $attemptCount = $examPrep->attempts()
            ->where('user_id', $user->id)
            ->count();

        return Inertia::render('Student/ExamPreps/Instructions', [
            'examPrep' => $examPrep->load(['examBoard', 'subject']),
            'attemptCount' => $attemptCount,
            'nextAttemptNumber' => $attemptCount + 1,
            'bestScore' => $examPrep->getUserBestAttempt($user)?->percentage ?? 0,
        ]);
    }

    /**
     * Start a new exam attempt.
     */
    public function start(ExamPrep $examPrep)
    {
        $user = auth()->user();

        if (!$examPrep->canUserAttempt($user)) {
            return redirect()->route('student.exam-preps.show', $examPrep)
                ->with('error', 'Cannot start new attempt. Maximum attempts reached.');
        }

        if (!$examPrep->enrolledUsers()->where('user_id', $user->id)->exists()) {
            $examPrep->enrollUser($user);
        }

        try {
            DB::beginTransaction();

            // Create new attempt
            $attempt = ExamPrepAttempt::create([
                'user_id' => $user->id,
                'exam_prep_id' => $examPrep->id,
                'attempt_number' => $examPrep->getUserAttemptCount($user) + 1,
                'questions' => $examPrep->generateQuestions(),
                'started_at' => now(),
            ]);

            // Update enrollment
            $examPrep->enrolledUsers()->updateExistingPivot($user->id, [
                'last_attempt_at' => now(),
            ]);

            DB::commit();

            return Inertia::render('Student/ExamPreps/Exam', [
                'examPrep' => $examPrep,
                'attempt' => $attempt,
                'currentQuestionIndex' => 0,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to start exam: ' . $e->getMessage());
        }
    }

    /**
     * Save answer for current question.
     */
    public function saveAnswer(ExamPrep $examPrep, ExamPrepAttempt $attempt, Request $request)
    {


        if ($attempt->completed_at) {
            return response()->json(['error' => 'Exam already submitted'], 400);
        }

        $validated = $request->validate([
            'question_index' => 'required|integer',
            'answer' => 'nullable',
        ]);

        $answers = $attempt->answers ?? [];
        $answers[$validated['question_index']] = $validated['answer'];

        $attempt->update(['answers' => $answers]);

        return response()->json(['success' => true]);
    }

    /**
     * Submit completed exam.
     */
    public function submit(ExamPrep $examPrep, ExamPrepAttempt $attempt, Request $request)
    {


        if ($attempt->completed_at) {
            return response()->json(['error' => 'Exam already submitted'], 400);
        }

        try {
            DB::beginTransaction();

            $answers = $request->validate(['answers' => 'required|array'])['answers'];

            $attempt->completeAttempt($answers);

            DB::commit();

             return response()->json([
                'success' => true,
                'message' => 'Exam submitted successfully',
                'redirect' => route('student.exam-preps.results', [$examPrep, $attempt])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to submit exam: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show exam results.
     */
    public function results(ExamPrep $examPrep, ExamPrepAttempt $attempt)
    {


        if (!$attempt->completed_at) {
            return redirect()->route('student.exam-preps.show', $examPrep);
        }

        return Inertia::render('Student/ExamPreps/Results', [
            'examPrep' => $examPrep->load(['examBoard', 'subject']),
            'attempt' => $attempt->load('user'),
            'canRetake' => $examPrep->canUserAttempt(auth()->user()),
            'nextAttemptNumber' => $attempt->attempt_number + 1,
        ]);
    }

    /**
     * Show student's exam attempts.
     */
    public function myAttempts(Request $request)
    {
        $user = auth()->user();

        $attempts = $user->examPrepAttempts()
            ->with(['examPrep.examBoard', 'examPrep.subject'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Student/ExamPreps/MyAttempts', [
            'attempts' => $attempts,
        ]);
    }

    /**
     * View specific attempt.
     */
    public function viewAttempt(ExamPrepAttempt $attempt)
    {


        return Inertia::render('Student/ExamPreps/ViewAttempt', [
            'attempt' => $attempt->load(['examPrep.examBoard', 'examPrep.subject']),
            'results' => $this->getDetailedResults($attempt),
        ]);
    }

    /**
     * Get detailed results for attempt.
     */
    private function getDetailedResults(ExamPrepAttempt $attempt)
    {
        $questions = $attempt->questions ?? [];
        $answers = $attempt->answers ?? [];
        $results = [];

        foreach ($questions as $index => $question) {
            $userAnswer = $answers[$index] ?? null;
            $correctAnswer = $question['correct_answer'] ?? null;

            $isCorrect = false;
            if ($userAnswer !== null) {
                $isCorrect = $this->isAnswerCorrect(
                    $userAnswer,
                    $correctAnswer,
                    $question['question_type'] ?? 'multiple_choice'
                );
            }

            $results[] = [
                'question_index' => $index,
                'question_text' => $question['question_text'],
                'options' => $question['options'] ?? [],
                'question_type' => $question['question_type'] ?? 'multiple_choice',
                'user_answer' => $userAnswer,
                'correct_answer' => $correctAnswer,
                'is_correct' => $isCorrect,
                'points' => $question['points'] ?? 1,
                'explanation' => $question['metadata']['explanation'] ?? null,
            ];
        }

        return $results;
    }

    /**
     * Check if answer is correct.
     */
    private function isAnswerCorrect($userAnswer, $correctAnswer, $questionType)
    {
        if ($userAnswer === null) return false;

        switch ($questionType) {
            case 'multiple_choice':
            case 'true_false':
                return trim(strval($userAnswer)) === trim(strval($correctAnswer));

            case 'multiple_answer':
                if (!is_array($userAnswer) || !is_array($correctAnswer)) {
                    return false;
                }
                sort($userAnswer);
                sort($correctAnswer);
                return $userAnswer === $correctAnswer;

            case 'short_answer':
                return strtolower(trim($userAnswer)) === strtolower(trim($correctAnswer));

            default:
                return trim(strval($userAnswer)) === trim(strval($correctAnswer));
        }
    }
}
