<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
     protected $fillable = [
        'name',
        'slug',
    ];


    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'subject_id');
    }

    public function examPreps(): HasMany
    {
        return $this->hasMany(ExamPrep::class, 'subject_id');
    }



    /**
     * Get the exam boards for this subject
     */
    public function examBoards(): BelongsToMany
    {
        return $this->belongsToMany(ExamBoard::class, 'exam_board_subject')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active subjects
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('id');
    }
}
