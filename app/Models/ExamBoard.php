<?php
// app/Models/ExamBoard.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamBoard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'country',
        'subjects',
        'is_active',
    ];

    protected $casts = [
        'subjects' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function studentProfiles(): HasMany
    {
        return $this->hasMany(StudentProfile::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    // Methods
    public function getAvailableSubjects()
    {
        return $this->subjects ?? [];
    }

    public function hasSubject($subject)
    {
        return in_array($subject, $this->getAvailableSubjects());
    }

    public function examPreps(): HasMany
    {
        return $this->hasMany(ExamPrep::class, 'exam_board_id');
    }

    /**
     * Get the subjects for this exam board
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'exam_board_subject')
            ->withTimestamps();
    }


}
