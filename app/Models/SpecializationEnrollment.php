<?php
// app/Models/SpecializationEnrollment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecializationEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialization_id',
        'user_id',
        'student_profile_id',
        'progress_percentage',
        'completed_courses',
        'selected_electives',
        'enrolled_at',
        'started_at',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'completed_courses' => 'array',
        'selected_electives' => 'array',
        'enrolled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    // Methods
    public function start()
    {
        $this->update([
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'progress_percentage' => 100,
        ]);
    }

    public function drop()
    {
        $this->update([
            'status' => 'dropped',
        ]);
    }

    public function updateProgress()
    {
        $specialization = $this->specialization;
        $completed = $this->completed_courses ?? [];

        $totalCourses = $specialization->requiredCourses()->count();
        $completedCount = count($completed);

        $progress = $totalCourses > 0
            ? ($completedCount / $totalCourses) * 100
            : 0;

        $this->progress_percentage = min($progress, 100);

        // Check if completed
        if ($this->progress_percentage >= 100 && !$this->completed_at) {
            $this->complete();
            $specialization->increment('completion_count');
        }

        $this->save();
    }

    public function markCourseCompleted($courseId)
    {
        $completed = $this->completed_courses ?? [];

        if (!in_array($courseId, $completed)) {
            $completed[] = $courseId;
            $this->completed_courses = $completed;
            $this->updateProgress();
        }
    }

    public function selectElective($courseId)
    {
        $selected = $this->selected_electives ?? [];

        if (!in_array($courseId, $selected)) {
            $selected[] = $courseId;
            $this->selected_electives = $selected;
            $this->save();
        }
    }
}
