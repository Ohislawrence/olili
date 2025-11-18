<?php
// app/Models/CourseOutline.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CourseOutline extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'description',
        'order',
        'type',
        'estimated_duration_minutes',
        'learning_objectives',
        'key_concepts',
        'resources',
        'has_quiz',
        'has_project',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'learning_objectives' => 'array',
        'key_concepts' => 'array',
        'resources' => 'array',
        'has_quiz' => 'boolean',
        'has_project' => 'boolean',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    // Fix the course relationship
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id')
            ->through('module');
    }

    // Or use an accessor (recommended):
    public function getCourseAttribute()
    {
        return $this->module->course;
    }

    public function contents(): HasMany
    {
        return $this->hasMany(CourseContent::class)->orderBy('order');
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    public function progressTracking(): HasMany
    {
        return $this->hasMany(ProgressTracking::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWithQuizzes($query)
    {
        return $query->where('has_quiz', true);
    }

    // Methods
    public function markAsCompleted()
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        // Update module and course progress
        $this->module->updateProgress();
        $this->module->course->updateProgress();
    }

    public function markAsIncomplete()
    {
        $this->update([
            'is_completed' => false,
            'completed_at' => null,
        ]);

        // Update module and course progress
        $this->module->updateProgress();
        $this->module->course->updateProgress();
    }

    public function getKeyConceptsArray()
    {
        return $this->key_concepts ?? [];
    }

    public function getResourcesArray()
    {
        return $this->resources ?? [];
    }

    public function getNextContent()
    {
        return $this->contents()
            ->orderBy('order')
            ->first();
    }

    public function getEstimatedReadTime()
    {
        return $this->contents->sum(function($content) {
            return $content->content_type === 'text' ?
                ceil(str_word_count(strip_tags($content->content)) / 200) : 0; // 200 words per minute
        });
    }
}
