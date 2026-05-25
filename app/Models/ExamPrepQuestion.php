<?php
// app/Models/ExamPrepQuestion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamPrepQuestion extends Model
{
    protected $fillable = [
        'quiz_id',
        'course_outline_id',
        'exam_prep_id',
        'question_text',
        'options',
        'correct_answer',
        'explanation',
        'question_type',
        'points',
        'difficulty',
        'order',
        'metadata',
        'times_used',
        'times_correct',
        'times_incorrect',

    ];

    protected $casts = [
        'options' => 'array',
        'metadata' => 'array',
        'times_used' => 'integer',
        'times_correct' => 'integer',
        'times_incorrect' => 'integer',
    ];

    // Relationships
    public function examPrep(): BelongsTo
    {
        return $this->belongsTo(ExamPrep::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamPrepAttemptQuestion::class);
    }

    // Methods
    public function recordAttempt(bool $isCorrect): void
    {
        $this->increment('times_used');

        if ($isCorrect) {
            $this->increment('times_correct');
        } else {
            $this->increment('times_incorrect');
        }
    }

    public function getSuccessRate(): float
    {
        if ($this->times_used === 0) {
            return 0;
        }

        return round(($this->times_correct / $this->times_used) * 100, 1);
    }

    public function getFormattedOptions(): array
    {
        $options = $this->options ?? [];

        // Ensure options are indexed from A, B, C, D
        $formatted = [];
        $letters = ['A', 'B', 'C', 'D'];

        foreach ($options as $index => $option) {
            $letter = $letters[$index] ?? chr(65 + $index);
            $formatted[$letter] = $option;
        }

        return $formatted;
    }

    public function isCorrectAnswer(string $answer): bool
    {
        return $this->correct_answer === $answer;
    }
}
