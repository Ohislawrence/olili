<?php
// app/Models/ChatMessage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_session_id',
        'sender_type',
        'message',
        'metadata',
        'ai_model_used',
        'token_count',
        'generation_cost',
        'is_related_to_topic',
    ];

    protected $casts = [
        'metadata' => 'array',
        'generation_cost' => 'decimal:6',
        'is_related_to_topic' => 'boolean',
    ];

    // Relationships
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    // Scopes
    public function scopeFromUser($query)
    {
        return $query->where('sender_type', 'user');
    }

    public function scopeFromAI($query)
    {
        return $query->where('sender_type', 'ai');
    }

    public function scopeFromTutor($query)
    {
        return $query->where('sender_type', 'tutor');
    }

    public function scopeRelatedToTopic($query)
    {
        return $query->where('is_related_to_topic', true);
    }

    // Methods
    public function isFromUser()
    {
        return $this->sender_type === 'user';
    }

    public function isFromAI()
    {
        return $this->sender_type === 'ai';
    }

    public function isFromTutor()
    {
        return $this->sender_type === 'tutor';
    }

    public function getFormattedMessage()
    {
        if ($this->isFromAI() && isset($this->metadata['formatted_response'])) {
            return $this->metadata['formatted_response'];
        }

        return nl2br(e($this->message));
    }

    public function getCostFormatted()
    {
        return '$' . number_format($this->generation_cost, 6);
    }

    public function markAsOffTopic()
    {
        $this->update(['is_related_to_topic' => false]);
    }
}
