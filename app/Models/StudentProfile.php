<?php
// app/Models/StudentProfile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_board_id',
        'current_level',
        'target_level',
        'target_completion_date',
        'learning_goals',
        'learning_preferences',
        'weekly_study_hours',
        'current_grade',
        'target_grade',
    ];

    protected $casts = [
        'target_completion_date' => 'date',
        'learning_goals' => 'array',
        'learning_preferences' => 'array',
        'current_grade' => 'decimal:1',
        'target_grade' => 'decimal:1',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function examBoard(): BelongsTo
    {
        return $this->belongsTo(ExamBoard::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    // Scopes
    public function scopeWithExamBoard($query, $examBoardId)
    {
        return $query->where('exam_board_id', $examBoardId);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('current_level', $level);
    }

    // Methods
    public function getLearningGoalsArray()
    {
        return $this->learning_goals ?? [];
    }

    public function getLearningPreferencesArray()
    {
        return $this->learning_preferences ?? [];
    }

    public function calculateProgress()
    {
        $totalCourses = $this->courses()->count();
        $completedCourses = $this->courses()->where('status', 'completed')->count();

        return $totalCourses > 0 ? ($completedCourses / $totalCourses) * 100 : 0;
    }

    public function getRecommendedStudyPlan()
    {
        $remainingWeeks = now()->diffInWeeks($this->target_completion_date);
        $totalStudyHours = $this->weekly_study_hours * $remainingWeeks;

        return [
            'remaining_weeks' => $remainingWeeks,
            'total_study_hours' => $totalStudyHours,
            'weekly_hours' => $this->weekly_study_hours,
        ];
    }
}
