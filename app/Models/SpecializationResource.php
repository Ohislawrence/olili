<?php
// app/Models/SpecializationResource.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecializationResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialization_id',
        'title',
        'type',
        'description',
        'file_url',
        'external_url',
        'download_count',
        'order',
        'is_free',
        'is_active',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_active' => 'boolean',
        'download_count' => 'integer',
        'order' => 'integer',
    ];

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }
}
