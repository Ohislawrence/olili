<?php
// app/Models/SpecializationCourse.php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SpecializationCourse extends Pivot
{
    protected $table = 'specialization_courses';

    protected $casts = [
        'is_required' => 'boolean',
        'order' => 'integer',
        'recommended_weeks' => 'integer',
    ];

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
