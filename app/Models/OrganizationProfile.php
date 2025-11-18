<?php
// app/Models/OrganizationProfile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'logo',
        'cover_image',
        'website',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'founded_year',
        'total_students',
        'total_tutors',
        'max_students',
        'max_tutors',
        'settings',
        'features',
        'is_verified',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'features' => 'array',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'founded_year' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tutors(): HasMany
    {
        return $this->hasMany(TutorProfile::class);
    }

    public function students(): HasMany
    {
        // Get all students through tutors in this organization
        return $this->hasMany(StudentProfile::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    // Methods
    public function getAddressFullAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function canAddMoreTutors(): bool
    {
        return $this->tutors()->count() < $this->max_tutors;
    }

    public function canAddMoreStudents(): bool
    {
        return $this->total_students < $this->max_students;
    }

    public function getTutorCount(): int
    {
        return $this->tutors()->count();
    }

    public function getStudentCount(): int
    {
        return $this->total_students;
    }

    public function getVerifiedTutors(): HasMany
    {
        return $this->tutors()->verified();
    }

    public function addTutor(TutorProfile $tutor): void
    {
        $tutor->update(['organization_id' => $this->id]);
        $this->increment('total_tutors');
    }

    public function removeTutor(TutorProfile $tutor): void
    {
        $tutor->update(['organization_id' => null]);
        $this->decrement('total_tutors');
    }

    public function updateStudentCount(): void
    {
        $studentCount = $this->tutors()->withCount('activeStudents')->get()->sum('active_students_count');
        $this->update(['total_students' => $studentCount]);
    }

    public function getActiveFeatures(): array
    {
        return $this->features ?? [];
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->getActiveFeatures());
    }
}
