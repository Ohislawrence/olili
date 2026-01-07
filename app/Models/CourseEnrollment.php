<?php
// app/Models/CourseEnrollment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Pivot
{
    use HasFactory;

    protected $table = 'course_enrollments';

    protected $fillable = [
        'course_id',
        'user_id',
        'student_profile_id',
        'progress_percentage',
        'completed_modules',
        'total_modules',
        'status',
        'enrolled_at',
        'dropped_at',
        'paused_at',
        'resumed_at',
        'started_at',
        'completed_at',
        'last_accessed_at',
        'personalized_data',
        'actual_duration_hours',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'paused_at'    => 'datetime',
        'dropped_at'   => 'datetime',
        'last_accessed_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'personalized_data' => 'array',
        'actual_duration_hours' => 'decimal:2',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function modules()
    {
        return $this->hasManyThrough(
        Module::class,
        Course::class,
        'id', // Foreign key on Course table
        'course_id', // Foreign key on Module table
        'course_id', // Local key on CourseEnrollment table
        'id' // Local key on Course table
    );
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function progressTracking()
    {
        return $this->hasMany(ProgressTracking::class, 'user_id', 'user_id')
            ->where('course_id', $this->course_id);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['enrolled', 'active']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function startCourse()
    {
        if (!$this->started_at) {
            $this->update([
                'status' => 'active',
                'started_at' => now(),
            ]);
        }
    }

    public function updateProgress()
    {
        $course = $this->course;

        // Calculate progress based on topics completed
        $totalTopics = $course->topics()->count();
        $completedTopics = 0;

        foreach ($course->modules as $module) {
            foreach ($module->topics as $topic) {
                if ($topic->isCompletedForUser($this->user_id)) {
                    $completedTopics++;
                }
            }
        }

        $progress = $totalTopics > 0 ? ($completedTopics / $totalTopics) * 100 : 0;

        // Calculate completed modules
        $completedModules = 0;
        foreach ($course->modules as $module) {
            $allTopicsCompleted = true;
            foreach ($module->topics as $topic) {
                if (!$topic->isCompletedForUser($this->user_id)) {
                    $allTopicsCompleted = false;
                    break;
                }
            }
            if ($allTopicsCompleted && $module->topics->count() > 0) {
                $completedModules++;
            }
        }

        $this->update([
            'progress_percentage' => $progress,
            'completed_modules' => $completedModules,
            'last_accessed_at' => now(),
        ]);

        // Auto-complete if all topics done
        if ($progress >= 100 && $this->status !== 'completed') {
            $this->complete();
        }
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'progress_percentage' => 100,
        ]);
    }

    public function pause()
    {
        $this->update(['status' => 'paused',
                        'paused_at' => now()]);
    }

    public function resume()
    {
        $this->update(['status' => 'active',
                        'resumed_at' => now()]);
    }

    public function drop()
    {
        $this->update(['status' => 'dropped',
                        'dropped_at' => now()]);

        // Decrement course enrollment count
        $this->course()->decrement('current_enrollment');
    }

    public function getTimeSpent()
    {
        if (!$this->started_at) {
            return 0;
        }

        $endTime = $this->completed_at ?? now();
        return $this->started_at->diffInHours($endTime);
    }
}
