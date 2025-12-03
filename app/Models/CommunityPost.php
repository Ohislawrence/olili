<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'title',
        'content',
        'type',
        'tags',
        'views',
        'like_count',
        'comment_count',
        'is_pinned',
        'is_approved',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_pinned' => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class)->whereNull('parent_id');
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function scopeDiscussions($query)
    {
        return $query->where('type', 'discussion');
    }

    public function scopeQuestions($query)
    {
        return $query->where('type', 'question');
    }

    public function scopeAchievements($query)
    {
        return $query->where('type', 'achievement');
    }

    public function scopeResources($query)
    {
        return $query->where('type', 'resource');
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('like_count')
                    ->orderByDesc('comment_count');
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function getExcerptAttribute(): string
    {
        return \Str::limit(strip_tags($this->content), 150);
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}