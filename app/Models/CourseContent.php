<?php
// app/Models/CourseContent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_outline_id',
        'content',
        'content_type',
        'order',
        'metadata',
        'ai_model_used',
        'generation_prompt',
        'token_count',
        'generation_cost',
    ];

    protected $casts = [
        'metadata' => 'array',
        'generation_prompt' => 'array',
        'generation_cost' => 'decimal:6',
    ];

    // Relationships
    public function courseOutline(): BelongsTo
    {
        return $this->belongsTo(CourseOutline::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('content_type', $type);
    }

    public function scopeTextContent($query)
    {
        return $this->scopeByType($query, 'text');
    }

    public function scopeVideoContent($query)
    {
        return $this->scopeByType($query, 'video');
    }

    // Methods
    public function getFormattedContent()
    {
        if ($this->content_type === 'text') {
            return nl2br(e($this->content));
        }

        return $this->content;
    }

    public function getReadTime()
    {
        if ($this->content_type === 'text') {
            $wordCount = str_word_count(strip_tags($this->content));
            return ceil($wordCount / 200); // 200 words per minute
        }

        return $this->metadata['duration_minutes'] ?? 0;
    }

    public function getMetadataValue($key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    public function isInteractive()
    {
        return $this->content_type === 'interactive';
    }
}
