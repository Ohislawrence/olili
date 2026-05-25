<?php
// app/Models/Specialization.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Specialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'target_exam',
        'target_career',
        'level',
        'estimated_duration_hours',
        'price',
        'discount_price',
        'has_discount',
        'learning_objectives',
        'prerequisites',
        'target_skills',
        'career_opportunities',
        'thumbnail_url',
        'banner_url',
        'is_featured',
        'is_public',
        'status',
        'enrollment_count',
        'completion_count',
        'min_electives',
        'max_electives',
        'sort_order',
        'published_at',
        'created_by_user_id',
    ];

    protected $casts = [
        'learning_objectives' => 'array',
        'prerequisites' => 'array',
        'target_skills' => 'array',
        'career_opportunities' => 'array',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'has_discount' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'estimated_duration_hours' => 'integer',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($specialization) {
            if (empty($specialization->slug)) {
                $specialization->slug = Str::slug($specialization->name);
            }
        });

        static::updating(function ($specialization) {
            if ($specialization->isDirty('name') && empty($specialization->slug)) {
                $specialization->slug = Str::slug($specialization->name);
            }
        });
    }

    // Relationships
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'specialization_courses')
            ->withPivot(['order', 'is_required', 'category', 'recommended_weeks', 'notes'])
            ->orderByPivot('order');
    }

    public function requiredCourses()
    {
        return $this->courses()->wherePivot('is_required', true);
    }

    public function electiveCourses()
    {
        return $this->courses()->wherePivot('is_required', false);
    }

    public function coreCourses()
    {
        return $this->courses()->wherePivot('category', 'core');
    }

    public function supplementaryCourses()
    {
        return $this->courses()->wherePivot('category', 'supplementary');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(SpecializationEnrollment::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(SpecializationResource::class)->orderBy('order');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(SpecializationTag::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function activeEnrollments()
    {
        return $this->enrollments()->where('status', 'active');
    }

    public function completedEnrollments()
    {
        return $this->enrollments()->where('status', 'completed');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_public', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->active();
    }

    public function scopeByExam($query, $exam)
    {
        return $query->where('target_exam', $exam)->active();
    }

    public function scopeByCareer($query, $career)
    {
        return $query->where('target_career', $career)->active();
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Attributes
    public function getTotalCoursesAttribute()
    {
        return $this->courses()->count();
    }

    public function getRequiredCoursesCountAttribute()
    {
        return $this->requiredCourses()->count();
    }

    public function getElectiveCoursesCountAttribute()
    {
        return $this->electiveCourses()->count();
    }

    public function getTotalDurationAttribute()
    {
        return $this->courses()->sum('estimated_duration_hours');
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->has_discount && $this->discount_price) {
            return [
                'original' => number_format($this->price, 2),
                'discount' => number_format($this->discount_price, 2),
                'savings' => number_format($this->price - $this->discount_price, 2),
                'discount_percentage' => round((($this->price - $this->discount_price) / $this->price) * 100),
            ];
        }

        return $this->price ? ['original' => number_format($this->price, 2)] : null;
    }

    public function getCompletionRateAttribute()
    {
        if ($this->enrollment_count === 0) return 0;
        return round(($this->completion_count / $this->enrollment_count) * 100, 1);
    }

    // Methods
    public function publish()
    {
        $this->update([
            'status' => 'active',
            'is_public' => true,
            'published_at' => now(),
        ]);
    }

    public function unpublish()
    {
        $this->update([
            'status' => 'draft',
            'is_public' => false,
        ]);
    }

    public function isPublished(): bool
    {
        return $this->status === 'active' && $this->is_public;
    }

    public function addCourse(Course $course, bool $isRequired = true, array $attributes = [])
    {
        $order = $this->courses()->max('order') + 1;

        $this->courses()->attach($course->id, array_merge([
            'order' => $order,
            'is_required' => $isRequired,
            'category' => $isRequired ? 'core' : 'elective',
        ], $attributes));

        // Update estimated duration
        $this->update([
            'estimated_duration_hours' => $this->getTotalDurationAttribute(),
        ]);
    }

    public function removeCourse(Course $course)
    {
        $this->courses()->detach($course->id);

        // Update estimated duration
        $this->update([
            'estimated_duration_hours' => $this->getTotalDurationAttribute(),
        ]);
    }

    public function reorderCourses(array $courseIds)
    {
        foreach ($courseIds as $order => $courseId) {
            $this->courses()->updateExistingPivot($courseId, ['order' => $order + 1]);
        }
    }

    public function canEnroll(User $user): bool
    {
        if (!$this->isPublished()) return false;

        // Check if already enrolled
        if ($this->enrollments()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // Check prerequisites
        if (!empty($this->prerequisites)) {
            // Add prerequisite checking logic here
        }

        return true;
    }

    public function enrollStudent(User $user): ?SpecializationEnrollment
    {
        if (!$this->canEnroll($user)) return null;

        try {
            \DB::beginTransaction();

            $enrollment = SpecializationEnrollment::create([
                'specialization_id' => $this->id,
                'user_id' => $user->id,
                'student_profile_id' => $user->studentProfile?->id,
                'status' => 'enrolled',
                'enrolled_at' => now(),
                'selected_electives' => [],
            ]);

            // Increment enrollment count
            $this->increment('enrollment_count');

            \DB::commit();
            return $enrollment;
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Failed to enroll student in specialization: ' . $e->getMessage());
            return null;
        }
    }
}
