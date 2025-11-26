<?php
// app/Models/Flashcard.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flashcard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'course_outline_id',
        'question',
        'answer',
        'explanation',
        'difficulty_level',
        'is_public',
        'study_interval',
        'next_review_date',
        'ease_factor',
        'repetitions',
        'interval',
        'last_studied_at',
        'flashcard_set_id'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'next_review_date' => 'date',
        'last_studied_at' => 'datetime',
        'study_interval' => 'integer',
        'ease_factor' => 'decimal:2',
        'repetitions' => 'integer',
        'interval' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function courseOutline(): BelongsTo
    {
        return $this->belongsTo(CourseOutline::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeForOutline($query, $outlineId)
    {
        return $query->where('course_outline_id', $outlineId);
    }

    public function scopeDueForReview($query)
    {
        return $query->where('next_review_date', '<=', now())
                    ->orWhereNull('next_review_date');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Methods
    public function updateSpacedRepetition($quality)
    {
        // Simplified SM-2 algorithm
        if ($quality >= 3) {
            if ($this->repetitions == 0) {
                $this->interval = 1;
            } elseif ($this->repetitions == 1) {
                $this->interval = 6;
            } else {
                $this->interval = round($this->interval * $this->ease_factor);
            }

            $this->repetitions++;
        } else {
            $this->repetitions = 0;
            $this->interval = 1;
        }

        // Update ease factor
        $this->ease_factor = max(1.3, $this->ease_factor + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02)));

        // Set next review date
        $this->next_review_date = now()->addDays($this->interval);
        $this->last_studied_at = now();

        $this->save();
    }

    public function isDueForReview(): bool
    {
        return !$this->next_review_date || $this->next_review_date <= now();
    }

    public function getDifficultyColor(): string
    {
        return match($this->difficulty_level) {
            'easy' => 'emerald',
            'medium' => 'amber',
            'hard' => 'rose',
            default => 'gray',
        };
    }
}
