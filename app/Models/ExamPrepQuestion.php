<?php
// app/Models/ExamPrepQuestion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamPrepQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_prep_id',
        'quiz_id',
        'course_outline_id',
        'question_text',
        'options',
        'correct_answer',
        'question_type',
        'points',
        'difficulty',
        'metadata',
        'times_used',
        'times_correct',
        'times_incorrect',
    ];

    protected $casts = [
        'options' => 'array',
        'metadata' => 'array',
        'points' => 'integer',
        'times_used' => 'integer',
        'times_correct' => 'integer',
        'times_incorrect' => 'integer',
    ];

    // Relationships
    public function examPrep(): BelongsTo
    {
        return $this->belongsTo(ExamPrep::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function courseOutline(): BelongsTo
    {
        return $this->belongsTo(CourseOutline::class);
    }

    // Methods
    public function getSuccessRate(): float
    {
        $totalAttempts = $this->times_correct + $this->times_incorrect;

        return $totalAttempts > 0 ?
            ($this->times_correct / $totalAttempts) * 100 : 0;
    }

    public function incrementUsage(bool $wasCorrect): void
    {
        $this->increment('times_used');

        if ($wasCorrect) {
            $this->increment('times_correct');
        } else {
            $this->increment('times_incorrect');
        }

        $this->save();
    }
}
