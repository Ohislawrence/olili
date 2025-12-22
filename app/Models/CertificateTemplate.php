<?php
// app/Models/CertificateTemplate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'template_type',
        'background_image',
        'background_color',
        'text_color',
        'accent_color',
        'font_family',
        'font_size',
        'layout_config',
        'is_active',
        'is_default',
        'organization_id',
        'created_by',
    ];

    protected $casts = [
        'layout_config' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    // Relationships
    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationProfile::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'template_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    // Methods
    public function getLayoutConfig()
    {
        $defaultConfig = [
            'logo_position' => 'top-center',
            'title_position' => 'center',
            'student_name_position' => 'center',
            'course_title_position' => 'center',
            'issue_date_position' => 'bottom-left',
            'certificate_number_position' => 'bottom-right',
            'signature_position' => 'bottom-center',
            'qr_code_position' => 'bottom-right',
        ];

        return array_merge($defaultConfig, $this->layout_config ?? []);
    }
}