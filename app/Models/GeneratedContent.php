<?php
// app/Models/GeneratedContent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_outline_id',
        'content',
        'content_type',
        'generated_at',
        'status',
        'word_count',
        'read_time_minutes',
        'metadata',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'metadata' => 'array',
        'word_count' => 'integer',
        'read_time_minutes' => 'integer',
    ];

    protected $attributes = [
        'status' => 'generated',
    ];

    /**
     * Get the topic this content belongs to
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(CourseOutline::class, 'course_outline_id');
    }

    /**
     * Scope for specific content types
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('content_type', $type);
    }

    /**
     * Scope for recently generated content
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('generated_at', '>=', now()->subDays($days));
    }

    /**
     * Get content preview (first 200 characters)
     */
    public function getPreviewAttribute(): string
    {
        $content = strip_tags($this->content);
        return strlen($content) > 200 ? substr($content, 0, 200) . '...' : $content;
    }
}
