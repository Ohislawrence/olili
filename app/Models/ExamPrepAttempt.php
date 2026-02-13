<?php
// app/Models/ExamPrepAttempt.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamPrepAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_prep_id',
        'attempt_number',
        'questions',
        'answers',
        'score',
        'percentage',
        'is_passed',
        'time_spent_seconds',
        'started_at',
        'completed_at',
        'results_breakdown',
        'ai_feedback',
    ];

    protected $casts = [
        'questions' => 'array',
        'answers' => 'array',
        'score' => 'integer',
        'percentage' => 'decimal:2',
        'is_passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_spent_seconds' => 'integer',
        'results_breakdown' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function examPrep(): BelongsTo
    {
        return $this->belongsTo(ExamPrep::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function startAttempt(): void
    {
        $this->update([
            'started_at' => now(),
            'attempt_number' => $this->getNextAttemptNumber(),
        ]);
    }

    public function completeAttempt(array $answers): void
    {
        $results = $this->calculateResults($answers);
        $timeSpent = $this->started_at ? now()->diffInSeconds($this->started_at) : 0;

        $this->update([
            'answers' => $answers,
            'score' => $results['score'],
            'percentage' => $results['percentage'],
            'is_passed' => $results['percentage'] >= $this->examPrep->passing_score,
            'time_spent_seconds' => $timeSpent,
            'completed_at' => now(),
            'results_breakdown' => $results['breakdown'],
        ]);

        // Update exam prep statistics
        $this->examPrep->completeUserAttempt($this->user, $this);
    }

    private function getNextAttemptNumber(): int
    {
        $lastAttempt = self::where('user_id', $this->user_id)
            ->where('exam_prep_id', $this->exam_prep_id)
            ->latest('attempt_number')
            ->first();

        return $lastAttempt ? $lastAttempt->attempt_number + 1 : 1;
    }

    private function calculateResults(array $userAnswers): array
    {
        $score = 0;
        $totalPoints = 0;
        $breakdown = [];

        // Use the questions stored in the attempt
        $questions = $this->questions ?? [];

        foreach ($questions as $index => $question) {
            $questionPoints = $question['points'] ?? 1;
            $totalPoints += $questionPoints;

            $userAnswer = $userAnswers[$index] ?? null;
            $correctAnswer = $question['correct_answer'] ?? null; // FIX: Get from question array

            $isCorrect = $this->isAnswerCorrect(
                $userAnswer,
                $correctAnswer,
                $question['question_type'] ?? 'multiple_choice'
            );

            if ($isCorrect) {
                $score += $questionPoints;
            }

            $breakdown[] = [
                'question_index' => $index,
                'question_text' => $question['question_text'],
                'user_answer' => $userAnswer,
                'correct_answer' => $correctAnswer, // Include correct answer in breakdown
                'is_correct' => $isCorrect,
                'points' => $questionPoints,
                'points_earned' => $isCorrect ? $questionPoints : 0,
            ];
        }

        $percentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;

        return [
            'score' => $score,
            'total_points' => $totalPoints,
            'percentage' => round($percentage, 2),
            'breakdown' => $breakdown,
        ];
    }

    private function isAnswerCorrect($userAnswer, $correctAnswer, $questionType): bool
    {
        if ($userAnswer === null) {
            return false;
        }

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

    public function getTimeSpentFormatted(): string
    {
        $hours = floor($this->time_spent_seconds / 3600);
        $minutes = floor(($this->time_spent_seconds % 3600) / 60);
        $seconds = $this->time_spent_seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %02dm %02ds', $hours, $minutes, $seconds);
        }

        return sprintf('%02dm %02ds', $minutes, $seconds);
    }

    public function generateAIFeedback(): string
    {
        // This would integrate with your AI service
        // For now, return basic feedback
        $performance = $this->percentage >= 80 ? 'excellent' :
                      ($this->percentage >= 70 ? 'good' :
                      ($this->percentage >= 60 ? 'average' : 'needs improvement'));

        $feedback = "You scored {$this->percentage}% on this exam preparation. ";
        $feedback .= "Your performance was {$performance}. ";

        if ($this->results_breakdown) {
            $weakAreas = collect($this->results_breakdown)
                ->where('is_correct', false)
                ->pluck('question_text')
                ->take(3)
                ->toArray();

            if (!empty($weakAreas)) {
                $feedback .= "Consider reviewing questions on: " . implode(', ', $weakAreas);
            }
        }

        return $feedback;
    }

    // Get the quiz related to this attempt (if any)
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Get total points for this attempt
    public function getTotalPointsAttribute()
    {
        $total = 0;
        foreach ($this->questions ?? [] as $question) {
            $total += $question['points'] ?? 1;
        }
        return $total;
    }

    // Get correct answers count
    public function getCorrectCountAttribute()
    {
        if (!$this->results_breakdown) {
            return 0;
        }

        return collect($this->results_breakdown)
            ->where('is_correct', true)
            ->count();
    }

    // Get incorrect answers count
    public function getIncorrectCountAttribute()
    {
        if (!$this->results_breakdown) {
            return 0;
        }

        return collect($this->results_breakdown)
            ->where('is_correct', false)
            ->count();
    }

    // Get unanswered count
    public function getUnansweredCountAttribute()
    {
        if (!$this->results_breakdown) {
            return 0;
        }

        return collect($this->results_breakdown)
            ->where('user_answer', null)
            ->count();
    }

}
