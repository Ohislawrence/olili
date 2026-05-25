<?php
// app/Models/Certificate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_number',
        'user_id',
        'course_id',
        'organization_id',
        'issued_by_type',
        'issued_by_id',
        'title',
        'description',
        'certificate_data',
        'issue_date',
        'expiry_date',
        'qr_code',
        'verification_url',
        'status',
        'download_count',
        'is_public',
        'metadata',
    ];

    protected $casts = [
        'certificate_data' => 'array',
        'issue_date' => 'datetime',
        'expiry_date' => 'datetime',
        'download_count' => 'integer',
        'is_public' => 'boolean',
        'metadata' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationProfile::class, 'organization_id');
    }

    public function issuedBy(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function($q) {
                $q->whereNotNull('expiry_date')
                  ->where('expiry_date', '<', now());
            });
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Methods
    public function generateCertificateNumber()
    {
        if (!$this->certificate_number) {
            $prefix = 'OLCERT';
            $year = date('Y');
            $sequence = str_pad(Certificate::whereYear('issue_date', $year)->count() + 1, 6, '0', STR_PAD_LEFT);
            $this->certificate_number = "{$prefix}-{$year}-{$sequence}";
        }
        return $this->certificate_number;
    }

    public function generateVerificationUrl()
    {
        if (!$this->verification_url) {
            $hash = hash('sha256', $this->certificate_number . $this->user_id . $this->course_id);
            $this->verification_url = route('certificates.verify', $hash);
        }
        return $this->verification_url;
    }

    public function isExpired()
    {
        if ($this->status === 'expired') {
            return true;
        }

        if ($this->expiry_date && $this->expiry_date->isPast()) {
            $this->update(['status' => 'expired']);
            return true;
        }

        return false;
    }

    public function getCertificateData()
    {
        $baseData = [
            'certificate_number' => $this->certificate_number,
            'student_name' => $this->user->name,
            'course_title' => $this->course->title,
            'title' => $this->title,
            'issue_date' => $this->issue_date->format('F j, Y'),
            'expiry_date' => $this->expiry_date ? $this->expiry_date->format('F j, Y') : null,
            'verification_url' => $this->verification_url,
            'qr_code' => $this->qr_code,
            'student_email' => $this->user->email,
        ];

        // Add organization/issuer data if applicable
        if ($this->organization_id) {
            $baseData['organization'] = [
                'name' => $this->organization->name,
                'logo' => $this->organization->logo,
                'verified' => $this->organization->is_verified,
                'website' => $this->organization->website ?? config('app.url'),
            ];
            $baseData['issued_by'] = $this->organization->name;
            $baseData['issuer'] = [
                'name' => $this->organization->name,
                'logo' => $this->organization->logo,
                'website' => $this->organization->website ?? config('app.url'),
            ];
        } else {
            $baseData['issued_by'] = 'Olilearn AI Learning Platform';
            $baseData['organization'] = null;
            $baseData['issuer'] = [
                'name' => 'Olilearn AI Learning Platform',
                'logo' => config('app.logo'),
                'website' => config('app.url'),
            ];
        }

        // Add student data
        $baseData['student'] = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'avatar' => $this->user->avatar_url,
        ];

        // Add course data
        $baseData['course'] = [
            'title' => $this->course->title,
            'subject' => $this->course->subject,
            'level' => $this->course->level,
            'completed_date' => $this->course->actual_completion_date?->format('F j, Y'),
            'progress_percentage' => $this->course->progress_percentage,
        ];

        // Add completion data
        $baseData['completion_data'] = [
            'completed_at' => $this->course->actual_completion_date?->format('F j, Y'),
            'score' => $this->course->progress_percentage,
            'modules_completed' => $this->course->modules()->where('is_completed', true)->count(),
            'total_modules' => $this->course->modules()->count(),
            'capstone_completed' => $this->course->capstoneProject?->is_approved ?? false,
            'total_hours' => $this->course->actual_duration_hours,
            'capstone_score' => $this->course->capstoneProject?->final_score ?? 100,
        ];

        // Add template configuration if available
        $template = $this->organization?->certificateTemplate ??
                    CertificateTemplate::where('is_default', true)->first();

        if ($template) {
            $baseData['template'] = $template->getLayoutConfig();
        }

        // Add image URL if it exists in metadata
        if ($this->metadata && isset($this->metadata['image_path'])) {
            $baseData['image_url'] = $this->metadata['image_path'];
        }

        // Add PDF URL if it exists in metadata
        if ($this->metadata && isset($this->metadata['pdf_path'])) {
            $baseData['pdf_url'] = $this->metadata['pdf_path'];
        }

        return array_merge($baseData, $this->certificate_data ?? []);
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function canDownload()
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function getShareableImageUrl(): string
    {
        // Check if image already exists in metadata
        if ($this->metadata && isset($this->metadata['image_path'])) {
            return $this->metadata['image_path'];
        }

        // If no image exists, generate it
        try {
            $service = app(\App\Services\CertificateGenerationService::class);
            return $service->generateShareableImage($this);
        } catch (\Exception $e) {
            \Log::error('Failed to generate certificate image: ' . $e->getMessage());

            // Return a placeholder or empty string
            return '';
        }
    }

    /**
     * Get the PDF download URL
     */
    public function getPdfUrl(): string
    {
        if ($this->metadata && isset($this->metadata['pdf_path'])) {
            return $this->metadata['pdf_path'];
        }

        return '';
    }

    /**
     * Check if certificate has a shareable image
     */
    public function hasShareableImage(): bool
    {
        return !empty($this->getShareableImageUrl());
    }

    /**
     * Regenerate the certificate image
     */
    public function regenerateImage(): bool
    {
        try {
            $service = app(\App\Services\CertificateGenerationService::class);
            return $service->regenerateCertificateImage($this);
        } catch (\Exception $e) {
            \Log::error('Failed to regenerate certificate image: ' . $e->getMessage());
            return false;
        }
    }
}
