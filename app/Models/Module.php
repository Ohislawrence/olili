<?php
// app/Models/Module.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'estimated_duration_minutes',
        'learning_objectives',
        //'is_completed',
        'completed_at',
        'needs_content_generation',
        'content_generated_at',
    ];

    protected $casts = [
        'learning_objectives' => 'array',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'needs_content_generation' => 'boolean',
        'content_generated_at' => 'datetime',
    ];

    protected $appends = ['is_completed'];

    // Relationships


    public function getIsCompletedAttribute(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Total topics under this module
        $totalTopics = $this->topics()->count();

        if ($totalTopics === 0) {
            return false;
        }

        // Topics completed by this user
        $completedTopics = $this->topics()
            ->whereHas('progressTrackings', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('activity_type', 'outline_completed');
            })
            ->count();

        return $completedTopics === $totalTopics;
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(CourseOutline::class)->orderBy('order');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    // Methods
    public function markAsCompleted()
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        // Update course progress
        $this->course->updateProgress();
    }

    public function markAsIncomplete()
    {
        $this->update([
            'is_completed' => false,
            'completed_at' => null,
        ]);

        // Update course progress
        $this->course->updateProgress();
    }

    public function getEstimatedDuration()
    {
        return $this->topics->sum('estimated_duration_minutes');
    }

    public function updateProgress()
    {
        $totalTopics = $this->topics()->count();
        $completedTopics = $this->topics()->where('is_completed', true)->count();

        $wasCompleted = $this->is_completed;
        $this->is_completed = $totalTopics > 0 && $completedTopics === $totalTopics;

        if ($this->is_completed && !$wasCompleted) {
            $this->completed_at = now();
        } elseif (!$this->is_completed) {
            $this->completed_at = null;
        }

        $this->save();
    }
}
