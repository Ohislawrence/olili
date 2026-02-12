<?php
// app/Models/ExamPrep.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ExamPrep extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'exam_board_id',
        'subject_id',
        'course_id',
        'total_questions',
        'time_limit_minutes',
        'passing_score',
        'max_attempts',
        'randomize_questions',
        'allow_pause',
        'show_results_immediately',
        'is_public',
        'status',
        'question_criteria',
        'question_distribution',
        'content_generation_status',
        'content_generation_job_id',
        'content_generation_started_at',
        'content_generation_completed_at',
        'ai_model_used',
        'generation_parameters',
        'generation_summary',
        'enrolled_count',
    ];

    protected $casts = [
        'randomize_questions' => 'boolean',
        'allow_pause' => 'boolean',
        'show_results_immediately' => 'boolean',
        'is_public' => 'boolean',
        'question_criteria' => 'array',
        'question_distribution' => 'array',
        'content_generation_started_at' => 'datetime',
        'content_generation_completed_at' => 'datetime',
        'generation_parameters' => 'array',
        'generation_summary' => 'array',
    ];

    // Relationships
    public function examBoard(): BelongsTo
    {
        return $this->belongsTo(ExamBoard::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamPrepQuestion::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamPrepAttempt::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(ExamPrepEnrollment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true)->where('status', 'active');
    }

    public function scopeGenerationPending($query)
    {
        return $query->where('content_generation_status', 'pending');
    }

    public function scopeGenerationProcessing($query)
    {
        return $query->where('content_generation_status', 'processing');
    }

    public function scopeGenerationCompleted($query)
    {
        return $query->where('content_generation_status', 'completed');
    }

    public function scopeGenerationFailed($query)
    {
        return $query->where('content_generation_status', 'failed');
    }

    // Methods
    public function isPublic(): bool
    {
        return $this->is_public && $this->status === 'active';
    }

    public function canEnroll(User $user): bool
    {
        if (!$this->isPublic()) {
            return false;
        }

        // Check if already enrolled and not dropped
        if ($this->enrollments()->where('user_id', $user->id)->where('status', '!=', 'dropped')->exists()) {
            return false;
        }

        // Check max attempts if enrollment is considered an attempt
        if ($this->max_attempts > 0) {
            $attemptCount = $this->attempts()->where('user_id', $user->id)->count();
            if ($attemptCount >= $this->max_attempts) {
                return false;
            }
        }

        return true;
    }

    public function incrementEnrolledCount(): void
    {
        $this->increment('enrolled_count');
    }

    public function getDifficultyDistribution(): array
    {
        return [
            'easy' => $this->questions()->where('difficulty', 'easy')->count(),
            'medium' => $this->questions()->where('difficulty', 'medium')->count(),
            'hard' => $this->questions()->where('difficulty', 'hard')->count(),
        ];
    }

    public function getQuestionTypeDistribution(): array
    {
        return $this->questions()
            ->select('question_type', \DB::raw('count(*) as total'))
            ->groupBy('question_type')
            ->pluck('total', 'question_type')
            ->toArray();
    }

    public function enrolledUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'exam_prep_enrollments')
            ->withPivot(['enrolled_at', 'status', 'last_attempt_at', 'best_score'])
            ->withTimestamps();
    }

    public function calculateStatistics(): void
    {
        $attempts = $this->attempts()->completed()->get();

        $this->update([
            'enrolled_count' => $this->enrolledUsers()->count(),
            'completed_count' => $attempts->count(),
            'average_score' => $attempts->avg('percentage') ?? 0,
        ]);
    }

    public function enrollUser(User $user): bool
    {
        if ($this->enrolledUsers()->where('user_id', $user->id)->exists()) {
            return false;
        }

        $this->enrolledUsers()->attach($user->id, [
            'enrolled_at' => now(),
            'status' => 'enrolled',
        ]);

        $this->increment('enrolled_count');

        return true;
    }

    public function completeUserAttempt(User $user, ExamPrepAttempt $attempt): void
    {
        $enrollment = $this->enrolledUsers()->where('user_id', $user->id)->first();

        if ($enrollment) {
            $pivot = $enrollment->pivot;

            // Update best score if this attempt is better
            $bestScore = $pivot->best_score ?? 0;
            if ($attempt->percentage > $bestScore) {
                $this->enrolledUsers()->updateExistingPivot($user->id, [
                    'best_score' => $attempt->percentage,
                    'last_attempt_at' => now(),
                ]);
            }
        }

        $this->calculateStatistics();
    }

    public function canUserAttempt(User $user): bool
    {
        if (!$this->isActive() || !$this->is_public) {
            return false;
        }

        if ($this->max_attempts > 0) {
            $attemptCount = $this->attempts()
                ->where('user_id', $user->id)
                ->count();

            if ($attemptCount >= $this->max_attempts) {
                return false;
            }
        }

        return true;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeForExamBoard($query, $examBoardId)
    {
        return $query->where('exam_board_id', $examBoardId);
    }

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function getUserAttemptCount(User $user): int
    {
        return $this->attempts()
            ->where('user_id', $user->id)
            ->count();
    }

    public function getUserBestAttempt(User $user): ?ExamPrepAttempt
    {
        return $this->attempts()
            ->where('user_id', $user->id)
            ->orderBy('percentage', 'desc')
            ->first();
    }

    public function usersWhoAttempted()
    {
        return $this->belongsToMany(User::class, 'exam_prep_attempts')
            ->withPivot(['percentage', 'is_passed', 'completed_at'])
            ->withTimestamps();
    }

    public function recentAttempts($limit = 10)
    {
        return $this->attempts()
            ->with('user')
            ->latest('completed_at')
            ->limit($limit)
            ->get();
    }

    public function topPerformers($limit = 5)
    {
        return $this->attempts()
            ->select('user_id', \DB::raw('MAX(percentage) as best_score'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('best_score', 'desc')
            ->limit($limit)
            ->get();
    }

}
