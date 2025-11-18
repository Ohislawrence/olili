<?php
// app/Models/Quiz.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_outline_id',
        'title',
        'description',
        'time_limit_minutes',
        'max_attempts',
        'passing_score',
        'is_active',
        'questions',
        'ai_model_used',
    ];

    protected $casts = [
        'questions' => 'array',
        'passing_score' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getQuestionsCountAttribute()
    {
        return count($this->questions ?? []);
    }



    // Relationships
    public function courseOutline(): BelongsTo
    {
        return $this->belongsTo(CourseOutline::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCourseOutline($query, $outlineId)
    {
        return $query->where('course_outline_id', $outlineId);
    }

    // Methods
    public function getQuestionsCount()
    {
        return count($this->questions ?? []);
    }

    public function getTotalPoints()
    {
        return collect($this->questions)->sum('points') ?? 0;
    }

    public function canUserAttempt($user)
    {
        if (!$this->is_active) {
            return false;
        }

        $attemptCount = $this->attempts()
            ->where('user_id', $user->id)
            ->count();

        // If max_attempts is 0, unlimited attempts
        if ($this->max_attempts === 0) {
            return true;
        }

        return $attemptCount < $this->max_attempts;
    }

    public function getUserBestAttempt($user)
    {
        return $this->attempts()
            ->where('user_id', $user->id)
            ->orderBy('percentage', 'desc')
            ->first();
    }

    public function calculateScore($userAnswers)
    {
        $totalPoints = 0;
        $earnedPoints = 0;



        foreach ($this->questions ?? [] as $index => $question) {
            $questionPoints = $question['points'] ?? 1;
            $totalPoints += $questionPoints;

            $userAnswer = $userAnswers[$index] ?? null;
            $correctAnswer = $question['correct_answer'] ?? null;
            $questionType = $question['type'] ?? 'multiple_choice';


            if ($this->isAnswerCorrect($userAnswer, $correctAnswer, $questionType)) {
                $earnedPoints += $questionPoints;
            }
        }

        $percentage = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;

        return [
            'score' => $earnedPoints,
            'total_points' => $totalPoints,
            'percentage' => $percentage
        ];
    }

    public function isAnswerCorrect($userAnswer, $correctAnswer, $questionType)
    {
        if ($userAnswer === null) {
            return false;
        }

        // Handle different question types
        switch ($questionType) {
            case 'multiple_choice':
            case 'true_false':
                return trim(strval($userAnswer)) === trim(strval($correctAnswer));

            case 'multiple_answer':
                if (!is_array($userAnswer) || !is_array($correctAnswer)) {
                    return false;
                }
                // Convert both arrays to sorted strings for comparison
                $userAnswersSorted = $userAnswer;
                $correctAnswersSorted = $correctAnswer;
                sort($userAnswersSorted);
                sort($correctAnswersSorted);
                return $userAnswersSorted === $correctAnswersSorted;

            case 'short_answer':
                // Case-insensitive comparison for short answers
                return strtolower(trim($userAnswer)) === strtolower(trim($correctAnswer));

            default:
                return trim(strval($userAnswer)) === trim(strval($correctAnswer));
        }
    }


}
