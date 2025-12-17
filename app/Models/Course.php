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
        'created_by', // 'admin' or 'student'
        'is_public',
        'enrollment_limit',
        'current_enrollment',
        'visibility',
        'slug',
        'is_shared',
        'shared_by',
        'created_by_user_id',

    ];

    protected $casts = [
        'start_date' => 'date',
        'target_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'progress_percentage' => 'decimal:2',
        'learning_objectives' => 'array',
        'prerequisites' => 'array',
        'generation_parameters' => 'array',
        'is_public' => 'boolean',
        'enrollment_limit' => 'integer',
        'current_enrollment' => 'integer',
        'visibility' => 'string', // 'public', 'private', 'unlisted'
    ];



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

    public function getAllTopicsAttribute()
    {
        return $this->modules->flatMap(function($module) {
            return $module->topics;
        });
    }

    public function flashcards()
    {
        return $this->hasMany(Flashcard::class);
    }

    public function flashcardSets()
    {
        return $this->hasMany(FlashcardSet::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function enrolledStudents()
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
            ->withPivot(['enrolled_at', 'progress_percentage', 'status'])
            ->withTimestamps();
    }

    // New scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true)
                    ->where('visibility', 'public');
    }

    public function scopeAvailableForEnrollment($query)
    {
        return $query->public()
                    ->where(function($q) {
                        $q->whereNull('enrollment_limit')
                        ->orWhereColumn('current_enrollment', '<', 'enrollment_limit');
                    });
    }

    public function scopeCreatedByAdmin($query)
    {
        return $query->where('created_by', 'admin');
    }

    public function scopeCreatedByStudent($query)
    {
        return $query->where('created_by', 'student');
    }

    // New methods
    public function isPublic(): bool
    {
        return $this->is_public && $this->visibility === 'public';
    }

    public function isFull(): bool
    {
        if (!$this->enrollment_limit) return false;
        return $this->current_enrollment >= $this->enrollment_limit;
    }



    public function enrollStudent(User $user): bool
    {
        if (!$this->canEnroll($user)) {
            return false;
        }

        try {
            \DB::beginTransaction();

            // Create enrollment record
            CourseEnrollment::create([
                'course_id' => $this->id,
                'user_id' => $user->id,
                'enrolled_at' => now(),
                'status' => 'active',
            ]);

            // Clone the course for the student
            $this->cloneForStudent($user);

            // Increment enrollment count
            $this->increment('current_enrollment');

            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Failed to enroll student: " . $e->getMessage());
            return false;
        }
    }

    private function cloneForStudent(User $user)
    {
        // Clone the course and all its content for the student
        $newCourse = $this->replicate();
        $newCourse->student_profile_id = $user->studentProfile->id;
        $newCourse->created_by = 'student';
        $newCourse->is_public = false;
        $newCourse->original_course_id = $this->id; // Track original public course
        $newCourse->save();

        // Clone modules
        foreach ($this->modules as $module) {
            $newModule = $module->replicate();
            $newModule->course_id = $newCourse->id;
            $newModule->save();

            // Clone topics
            foreach ($module->topics as $topic) {
                $newTopic = $topic->replicate();
                $newTopic->module_id = $newModule->id;
                $newTopic->save();

                // Clone contents
                foreach ($topic->contents as $content) {
                    $newContent = $content->replicate();
                    $newContent->course_outline_id = $newTopic->id;
                    $newContent->save();
                }

                // Clone quiz if exists
                if ($topic->quiz) {
                    $newQuiz = $topic->quiz->replicate();
                    $newQuiz->course_outline_id = $newTopic->id;
                    $newQuiz->save();

                    // Clone quiz questions
                    foreach ($topic->quiz->questions as $question) {
                        $newQuestion = $question->replicate();
                        $newQuestion->quiz_id = $newQuiz->id;
                        $newQuestion->save();
                    }
                }
            }
        }
    }
    public function canEnroll(User $user): bool
    {
        if (!$this->isPublic()) return false;
        if ($this->isFull()) return false;
        if ($this->enrolledStudents()->where('user_id', $user->id)->exists()) return false;

        return true;
    }

    public function shares()
    {
        return $this->hasMany(CourseShare::class);
    }

    public function sharedWithMe()
    {
        return $this->hasMany(CourseShare::class, 'recipient_id', 'created_by');
    }

    // Add a method to clone course for sharing
    public function cloneForUser(User $user, int $sharerId = null): Course
    {
        $clonedCourse = $this->replicate();
        $clonedCourse->created_by = $user->id;
        $clonedCourse->student_profile_id = $user->studentProfile->id;
        $clonedCourse->original_course_id = $this->id;
        $clonedCourse->is_shared = true;
        $clonedCourse->shared_by = $sharerId;
        $clonedCourse->status = 'draft';
        $clonedCourse->save();

        // Clone modules
        foreach ($this->modules as $module) {
            $clonedModule = $module->replicate();
            $clonedModule->course_id = $clonedCourse->id;
            $clonedModule->save();

            // Clone topics
            foreach ($module->topics as $topic) {
                $clonedTopic = $topic->replicate();
                $clonedTopic->module_id = $clonedModule->id;
                $clonedTopic->save();
            }
        }

        return $clonedCourse;
    }

    public function scopeSharedWith($query, User $user)
    {
        return $query->where(function($q) use ($user) {
            $q->whereHas('shares', function($query) use ($user) {
                $query->where('recipient_email', $user->email)
                    ->orWhere('recipient_id', $user->id);
            });
        });
    }

    public function scopeAcceptedShares($query, User $user)
    {
        return $query->whereHas('shares', function($query) use ($user) {
            $query->where(function($q) use ($user) {
                $q->where('recipient_email', $user->email)
                    ->orWhere('recipient_id', $user->id);
            })
            ->where('status', 'accepted');
        });
    }
}
