<?php
// app/Models/Course.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough; // Add this import

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_profile_id',
        'exam_board_id',
        'title',
        'subject',
        'description',
        'level',
        'status',
        'start_date',
        'target_completion_date',
        'actual_completion_date',
        'estimated_duration_hours',
        'actual_duration_hours',
        'progress_percentage',
        'learning_objectives',
        'prerequisites',
        'ai_model_used',
        'generation_parameters',
    ];

    protected $casts = [
        'start_date' => 'date',
        'target_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'progress_percentage' => 'decimal:2',
        'learning_objectives' => 'array',
        'prerequisites' => 'array',
        'generation_parameters' => 'array',
    ];

    // Remove this line as it might cause issues with the new structure
    // protected $with = ['outlines'];

    public function scopeWithProgress($query)
    {
        return $query->withCount(['modules', 'modules as completed_modules_count' => function ($q) {
            $q->where('is_completed', true);
        }]);
    }

    // Relationships
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    public function topics(): HasManyThrough
    {
        return $this->hasManyThrough(
            CourseOutline::class,
            Module::class,
            'course_id', // Foreign key on modules table
            'module_id', // Foreign key on course_outlines table
            'id', // Local key on courses table
            'id' // Local key on modules table
        );
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function examBoard(): BelongsTo
    {
        return $this->belongsTo(ExamBoard::class);
    }

    // Remove these outdated relationships
    public function outlines()
    {
        return $this->modules()->orderBy('order');
     }

    public function capstoneProject(): HasOne
    {
        return $this->hasOne(CapstoneProject::class);
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    public function progressTracking(): HasMany
    {
        return $this->hasMany(ProgressTracking::class);
    }

    // Remove these commented out methods
    // public function modules()
    // public function topics()

    public function quizzes(): HasManyThrough
    {
        return $this->hasManyThrough(
            Quiz::class,
            CourseOutline::class,
            'module_id', // Foreign key on course_outlines table
            'course_outline_id', // Foreign key on quizzes table
            'id', // Local key on courses table
            'id' // Local key on course_outlines table
        )->whereHas('courseOutline', function ($query) {
            $query->where('has_quiz', true);
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeBySubject($query, $subject)
    {
        return $query->where('subject', $subject);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeDueSoon($query)
    {
        return $query->where('target_completion_date', '<=', now()->addDays(7))
                    ->where('status', 'active');
    }

    // Methods
    public function updateProgress()
    {
        $totalModules = $this->modules()->count();
        $completedModules = $this->modules()->where('is_completed', true)->count();

        $this->progress_percentage = $totalModules > 0 ? ($completedModules / $totalModules) * 100 : 0;

        if ($this->progress_percentage >= 100) {
            $this->status = 'completed';
            $this->actual_completion_date = now();
        }

        $this->save();
    }

    public function getNextTopic()
    {
        // Get all modules with their pending topics
        $modules = $this->modules()
            ->with(['topics' => function ($query) {
                $query->where('is_completed', false)
                      ->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        foreach ($modules as $module) {
            if ($module->topics->count() > 0) {
                return $module->topics->first();
            }
        }

        return null;
    }

    public function getDurationStats()
    {
        $estimatedHours = $this->estimated_duration_hours;
        $actualHours = $this->actual_duration_hours;
        $completionRate = $estimatedHours > 0 ? ($actualHours / $estimatedHours) * 100 : 0;

        return [
            'estimated_hours' => $estimatedHours,
            'actual_hours' => $actualHours,
            'completion_rate' => $completionRate,
            'is_behind_schedule' => $completionRate > 100,
        ];
    }

    public function getLearningObjectivesArray()
    {
        return $this->learning_objectives ?? [];
    }

    public function sendDueNotification(): void
    {
        if ($this->status !== 'active') {
            return;
        }

        $notificationService = app(\App\Services\CourseNotificationService::class);

        if ($this->target_completion_date->isFuture()) {
            $daysRemaining = now()->diffInDays($this->target_completion_date);
            if ($daysRemaining <= 7) {
                $notificationService->sendDueSoonNotification($this, $daysRemaining);
            }
        } else {
            $notificationService->sendImmediateOverdueNotification($this);
        }
    }
}
