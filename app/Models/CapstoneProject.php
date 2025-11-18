<?php
// app/Models/CapstoneProject.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapstoneProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'requirements',
        'evaluation_criteria',
        'due_date',
        'status',
        'student_submission',
        'submission_files',
        'submitted_at',
        'ai_score',
        'tutor_score',
        'final_score',
        'ai_feedback',
        'tutor_feedback',
        'ai_model_used',
    ];

    protected $casts = [
        'requirements' => 'array',
        'evaluation_criteria' => 'array',
        'due_date' => 'date',
        'submitted_at' => 'datetime',
        'ai_score' => 'decimal:2',
        'tutor_score' => 'decimal:2',
        'final_score' => 'decimal:2',
        'ai_feedback' => 'array',
        'submission_files' => 'array',
    ];

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function studentProfile()
    {
        return $this->course->studentProfile;
    }

    // Scopes
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereIn('status', ['assigned', 'in_progress']);
    }

    // Methods
    public function submitProject($submission, $files = [])
    {
        $this->update([
            'student_submission' => $submission,
            'submission_files' => $files,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);
    }

    public function gradeByAI($score, $feedback)
    {
        $this->update([
            'ai_score' => $score,
            'ai_feedback' => $feedback,
            'ai_model_used' => config('ai.default_provider'),
        ]);

        $this->calculateFinalScore();
    }

    public function gradeByTutor($score, $feedback)
    {
        $this->update([
            'tutor_score' => $score,
            'tutor_feedback' => $feedback,
        ]);

        $this->calculateFinalScore();
    }

    private function calculateFinalScore()
    {
        if ($this->ai_score !== null && $this->tutor_score !== null) {
            $this->final_score = ($this->ai_score + $this->tutor_score) / 2;
        } elseif ($this->ai_score !== null) {
            $this->final_score = $this->ai_score;
        } elseif ($this->tutor_score !== null) {
            $this->final_score = $this->tutor_score;
        }

        if ($this->final_score !== null) {
            $this->status = 'graded';
        }

        $this->save();
    }

    public function isGraded()
    {
        return $this->status === 'graded';
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() &&
               !in_array($this->status, ['submitted', 'graded']);
    }

    public function getEvaluationCriteriaArray()
    {
        return $this->evaluation_criteria ?? [];
    }
}
