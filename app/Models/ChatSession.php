<?php
// app/Models/ChatSession.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'course_outline_id',
        'topic_context',
        'context_parameters',
        'is_active',
        'last_activity_at',
    ];

    protected $casts = [
        'context_parameters' => 'array',
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
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

    public function courseOutline(): BelongsTo
    {
        return $this->belongsTo(CourseOutline::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('last_activity_at', '>=', now()->subHours($hours));
    }

    // Methods
    public function addMessage($senderType, $message, $metadata = [], $aiModel = null)
    {
        $chatMessage = $this->messages()->create([
            'sender_type' => $senderType,
            'message' => $message,
            'metadata' => $metadata,
            'ai_model_used' => $aiModel,
        ]);

        $this->update([
            'last_activity_at' => now(),
        ]);

        return $chatMessage;
    }

    public function getRecentMessages($limit = 10)
    {
        return $this->messages()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse();
    }

    public function closeSession()
    {
        $this->update([
            'is_active' => false,
        ]);
    }

    public function getContextSummary()
    {
        return [
            'course' => $this->course?->title,
            'topic' => $this->courseOutline?->title ?? $this->topic_context,
            'messages_count' => $this->messages->count(),
            'last_activity' => $this->last_activity_at,
        ];
    }
}
