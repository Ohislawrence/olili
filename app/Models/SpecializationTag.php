<?php
// app/Models/SpecializationTag.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecializationTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialization_id',
        'tag',
    ];

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }
}
