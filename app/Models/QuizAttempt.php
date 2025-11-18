<?php
// app/Models/QuizAttempt.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'attempt_number',
        'answers',
        'score',
        'percentage',
        'is_passed',
        'started_at',
        'completed_at',
        'time_taken_seconds',
        'ai_feedback',
        'manual_feedback',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'is_passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'ai_feedback' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    // Scopes
    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('is_passed', false);
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function startAttempt()
    {
        $this->update([
            'started_at' => now(),
            'attempt_number' => $this->getNextAttemptNumber(),
        ]);
    }

    public function completeAttempt($answers, $scoreData)
    {
        $this->update([
            'answers' => $answers,
            'score' => $scoreData['score'],
            'percentage' => $scoreData['percentage'],
            'is_passed' => $scoreData['percentage'] >= $this->quiz->passing_score,
            'completed_at' => now(),
            'time_taken_seconds' => $this->started_at ? now()->diffInSeconds($this->started_at) : 0,
        ]);
    }

    private function getNextAttemptNumber()
    {
        $lastAttempt = self::where('user_id', $this->user_id)
            ->where('quiz_id', $this->quiz_id)
            ->orderBy('attempt_number', 'desc')
            ->first();

        return $lastAttempt ? $lastAttempt->attempt_number + 1 : 1;
    }

    public function getTimeTakenFormatted()
    {
        $minutes = floor($this->time_taken_seconds / 60);
        $seconds = $this->time_taken_seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getDetailedFeedback()
    {
        $feedback = $this->ai_feedback ?? [];

        if ($this->manual_feedback) {
            $feedback['tutor_feedback'] = $this->manual_feedback;
        }

        return $feedback;
    }
}
