<?php
// app/Models/ProgressTracking.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressTracking extends Model
{
    use HasFactory;

    protected $table = 'progress_tracking';

    protected $fillable = [
        'user_id',
        'course_id',
        'course_outline_id',
        'activity_type',
        'time_spent_minutes',
        'is_completed',
        'completion_score',
        'performance_metrics',
        'notes',
    ];

    protected $casts = [
        'time_spent_minutes' => 'decimal:2',
        'is_completed' => 'boolean',
        'completion_score' => 'decimal:2',
        'performance_metrics' => 'array',
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
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeByActivityType($query, $activityType)
    {
        return $query->where('activity_type', $activityType);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function recordActivity($minutes, $completed = false, $score = null, $metrics = [])
    {
        $this->update([
            'time_spent_minutes' => $this->time_spent_minutes + $minutes,
            'is_completed' => $completed,
            'completion_score' => $score,
            'performance_metrics' => array_merge($this->performance_metrics ?? [], $metrics),
        ]);


    }

    public function getPerformanceMetricsArray()
    {
        return $this->performance_metrics ?? [];
    }

    public function getTimeSpentFormatted()
    {
        $hours = floor($this->time_spent_minutes / 60);
        $minutes = $this->time_spent_minutes % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        }

        return sprintf('%dm', $minutes);
    }
}
