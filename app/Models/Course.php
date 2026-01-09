<?php
// app/Models/Course.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Services\CourseCodeService;
use App\Services\ProgressTrackingService;
use Carbon\Carbon;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'exam_board_id',
        'title',
        'slug',
        'thumbnail_url',
        'subject',
        'description',
        'syllabus',
        'level',
        'tags',
        'status',
        'start_date',
        'target_completion_date',
        'estimated_duration_hours',
        'learning_objectives',
        'prerequisites',
        'ai_model_used',
        'generation_parameters',
        'created_by',
        'created_by_user_id',
        'is_public',
        'visibility',
        'enrollment_limit',
        'current_enrollment',
        'price',
        'is_paid',
        'is_shared',
        'shared_by',
        'needs_content_generation',
        'content_generated_at',
        'content_generation_status',
        'content_generation_summary',
        'content_generation_job_id',
        'content_generation_started_at',
        'content_generation_retry_count',
        'quiz_generated_at',
        'quiz_generation_status',
        'quiz_generation_summary',
        'quiz_generation_job_id',
        'quiz_generation_started_at',
        'quiz_generation_retry_count',
        'est_completion_time',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'target_completion_date' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'learning_objectives' => 'array',
        'prerequisites' => 'array',
        'generation_parameters' => 'array',
        'tags' => 'array',
        'is_public' => 'boolean',
        'is_paid' => 'boolean',
        'enrollment_limit' => 'integer',
        'current_enrollment' => 'integer',
        'price' => 'decimal:2',
        'visibility' => 'string',
        'needs_content_generation' => 'boolean',
    'content_generated_at' => 'datetime',
    'quiz_generated_at' => 'datetime',
    'content_generation_started_at' => 'datetime',
    'quiz_generation_started_at' => 'datetime',
    'content_generation_summary' => 'array',
    'quiz_generation_summary' => 'array',
    ];

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
            'course_id',
            'module_id',
            'id',
            'id'
        );
    }

    public function examBoard(): BelongsTo
    {
        return $this->belongsTo(ExamBoard::class);
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
            ->withPivot(['status', 'progress_percentage', 'enrolled_at', 'completed_at'])
            ->withTimestamps();
    }

    public function enrolled()
    {
        return $this->enrollments()->whereIn('status', ['enrolled']);
    }

    public function activeEnrollments()
    {
        return $this->enrollments()->whereIn('status', [ 'active']);
    }

    public function completedEnrollments()
    {
        return $this->enrollments()->where('status', 'completed');
    }

    public function capstoneProject(): HasOne
    {
        return $this->hasOne(CapstoneProject::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true)
                    ->where('visibility', 'public');
    }

    public function scopeAvailableForEnrollment($query)
    {
        return $query->public()
                    ->where('status', 'active')
                    ->where(function($q) {
                        $q->whereNull('enrollment_limit')
                          ->orWhereColumn('current_enrollment', '<', 'enrollment_limit');
                    });
    }

    public function scopeFree($query)
    {
        return $query->where('is_paid', false)->orWhere('price', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true)->where('price', '>', 0);
    }

    // Methods
    public function isPublic(): bool
    {
        return $this->is_public && $this->visibility === 'public';
    }

    public function isFull(): bool
    {
        if (!$this->enrollment_limit) return false;
        return $this->current_enrollment >= $this->enrollment_limit;
    }

    public function canEnroll(User $user): bool
    {
        // Check if course is available
        if (!$this->isPublic() || $this->status !== 'active') {
            return false;
        }

        // Check if course is full
        if ($this->isFull()) {
            return false;
        }

        // Check if user is already enrolled
        if ($this->enrollments()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // Check payment requirements
        if ($this->is_paid && $this->price > 0) {
            // Add payment check logic here
            // For now, just return false
            return false;
        }

        return true;
    }

    public function enrollStudent(User $user, bool $isMassEnrollment = false): ?CourseEnrollment
    {
        // For mass enrollment, skip some checks
        if (!$isMassEnrollment && !$this->canEnroll($user)) {
            return null;
        }

        $numberOfWeeks = $this->estimated_duration_hours / 6;
        $expectedCompletionDate = Carbon::now()->addWeeks(ceil($numberOfWeeks));

        try {
            \DB::beginTransaction();

            // Check if already enrolled (not dropped)
            $existingEnrollment = $this->enrollments()
                ->where('user_id', $user->id)
                ->where('status', '!=', 'dropped')
                ->first();

            if ($existingEnrollment) {
                return null; // Already enrolled
            }

            // Create enrollment record
            $enrollment = CourseEnrollment::create([
                'course_id' => $this->id,
                'user_id' => $user->id,
                'student_profile_id' => $user->studentProfile?->id,
                'status' => 'enrolled',
                'enrolled_at' => now(),
                'total_modules' => $this->modules()->count(),
                'est_completion_time' => $expectedCompletionDate,
            ]);

            // Increment enrollment count
            $this->increment('current_enrollment');

            \DB::commit();
            return $enrollment;
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Failed to enroll student: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Optional: Clone course content for student personalization
     */


    // Progress tracking methods for the course as a whole
    public function getAverageProgress()
    {
        return $this->enrollments()->avg('progress_percentage') ?? 0;
    }

    public function getCompletionRate()
    {
        $total = $this->enrollments()->count();
        $completed = $this->completedEnrollments()->count();

        return $total > 0 ? ($completed / $total) * 100 : 0;
    }

    // Course management methods
    public function publish()
    {
        $this->update([
            'status' => 'active',
            'is_public' => true,
            'visibility' => 'public',
            'start_date' => now(),
        ]);
    }

    public function unpublish()
    {
        $this->update([
            'status' => 'draft',
            'is_public' => false,
        ]);
    }

    // Certificate methods
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function generateCertificateForUser(User $user): ?Certificate
    {
        $enrollment = $this->enrollments()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->first();

        if (!$enrollment) {
            return null;
        }

        return Certificate::create([
            'course_id' => $this->id,
            'user_id' => $user->id,
            'course_enrollment_id' => $enrollment->id,
            'completion_date' => $enrollment->completed_at,
            'grade' => $this->calculateGrade($enrollment),
            'certificate_number' => $this->generateCertificateNumber(),
            'status' => 'active',
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->code)) {
                $codeService = app(CourseCodeService::class);
                $course->code = $codeService->generate($course, 'standard');
            }
        });

        static::updating(function ($course) {
            if ($course->isDirty(['subject', 'exam_board_id', 'level'])) {
                $codeService = app(CourseCodeService::class);
                $course->code = $codeService->generate($course, 'standard');
            }
        });
    }

    public function updateProgress()
    {
        // This method can update course-level progress statistics if needed
        // For example, you might want to update enrollment progress percentages

        $enrollments = $this->enrollments()->whereIn('status', ['active', 'started'])->get();

        foreach ($enrollments as $enrollment) {
            // Calculate progress for this enrollment
            $progress = app(ProgressTrackingService::class)->calculateCourseProgress($this, $enrollment->user_id);

            // Update enrollment progress percentage
            $enrollment->update([
                'progress_percentage' => $progress['overall_completion_percentage']
            ]);

            // Check if course is completed
            if ($progress['overall_completion_percentage'] >= 100 && !$enrollment->completed_at) {
                $enrollment->complete();
            }
        }

        return $this;
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    public function progressTracking(): HasMany
    {
        return $this->hasMany(ProgressTracking::class);
    }


}
