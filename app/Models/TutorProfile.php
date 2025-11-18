<?php
// app/Models/TutorProfile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TutorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_id',
        'qualification',
        'bio',
        'specialties',
        'teaching_style',
        'years_of_experience',
        'hourly_rate',
        'timezone',
        'availability',
        'is_verified',
        'is_online',
        'max_students',
        'preferences',
        'profile_image',
        'cover_image',
        'rating',
        'total_reviews',
        'completed_sessions',
    ];

    protected $casts = [
        'specialties' => 'array',
        'availability' => 'array',
        'preferences' => 'array',
        'is_verified' => 'boolean',
        'is_online' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    protected $appends = ['full_name', 'average_rating'];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationProfile::class, 'organization_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(StudentProfile::class, 'tutor_student')
            ->withPivot('enrolled_at', 'status', 'notes', 'goals')
            ->withTimestamps();
    }

    public function activeStudents(): BelongsToMany
    {
        return $this->students()->wherePivot('status', 'active');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TutorReview::class);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeBySpecialty($query, $specialty)
    {
        return $query->whereJsonContains('specialties', $specialty);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_online', true)
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            });
    }

    // Methods
    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->rating;
    }

    public function canAcceptMoreStudents(): bool
    {
        return $this->activeStudents()->count() < $this->max_students;
    }

    public function getStudentCount(): int
    {
        return $this->activeStudents()->count();
    }

    public function getCompletionRate(): float
    {
        $totalSessions = $this->completed_sessions + $this->activeStudents()->count();
        return $totalSessions > 0 ? ($this->completed_sessions / $totalSessions) * 100 : 0;
    }

    public function getWeeklyAvailability(): array
    {
        return $this->availability ?? [
            'monday' => [],
            'tuesday' => [],
            'wednesday' => [],
            'thursday' => [],
            'friday' => [],
            'saturday' => [],
            'sunday' => [],
        ];
    }

    public function addStudent(StudentProfile $student, array $goals = []): void
    {
        $this->students()->attach($student->id, [
            'goals' => $goals,
            'enrolled_at' => now(),
        ]);
    }

    public function removeStudent(StudentProfile $student): void
    {
        $this->students()->updateExistingPivot($student->id, [
            'status' => 'cancelled',
        ]);
    }

    public function updateRating(): void
    {
        $averageRating = $this->reviews()->avg('rating');
        $this->update([
            'rating' => $averageRating ?? 0,
            'total_reviews' => $this->reviews()->count(),
        ]);
    }
}
