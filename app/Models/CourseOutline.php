<?php
// app/Models/CourseOutline.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Services\ProgressTrackingService;

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
        //'is_completed',
        'completed_at',
        'needs_content_generation',
        'content_generated_at',
        'needs_quiz_generation',
        'quiz_generated_at',
    ];

    protected $casts = [
        'learning_objectives' => 'array',
        'key_concepts' => 'array',
        'resources' => 'array',
        'has_quiz' => 'boolean',
        'has_project' => 'boolean',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'needs_content_generation' => 'boolean',
        'needs_quiz_generation' => 'boolean',
        'content_generated_at' => 'datetime',
        'quiz_generated_at' => 'datetime',
    ];

    protected $appends = ['is_completed'];

    // Relationships
    public function progressTrackings()
    {
        return $this->hasMany(ProgressTracking::class, 'course_outline_id');
    }

    public function getIsCompletedAttribute(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $this->progressTrackings()
            ->where('user_id', $user->id)
            ->where('activity_type', 'outline_completed')
            ->exists();
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id')
            ->through('module');
    }

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

    /**
     * Check if this topic is completed for a specific user
     */
    public function markAsCompletedForUser(User $user, CourseEnrollment $enrollment = null, $activityType = 'topic_complete', $minutes = 0, $score = null)
    {
        // Get the course from the module
        $course = $this->module->course;

        // Get enrollment if not provided
        if (!$enrollment && $user->studentProfile) {
            $enrollment = $user->enrollments()
                ->where('course_id', $course->id)
                ->first();
        }

        // Use the ProgressTrackingService
        $progressService = app(ProgressTrackingService::class);

        // Record the completion activity
        $progress = $progressService->recordActivity(
            user: $user,
            course: $course,
            enrollmentId: $enrollment ? $enrollment->id : null,
            outlineId: $this->id,
            activityType: $activityType,
            minutes: $minutes,
            completed: true,
            score: $score
        );

        // Also mark the outline as completed in the database
        if (!$this->is_completed) {
            $this->markAsCompleted();
        }

        return $progress;
    }

    /**
     * Record user activity on this topic (viewing, studying, etc.)
     */
    public function recordUserActivity(User $user, $activityType, $minutes, $completed = false, $score = null)
    {
        $course = $this->module->course;

        // Try to find user's enrollment
        $enrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->first();

        $progressService = app(ProgressTrackingService::class);

        return $progressService->recordActivity(
            user: $user,
            course: $course,
            enrollmentId: $enrollment ? $enrollment->id : null,
            outlineId: $this->id,
            activityType: $activityType,
            minutes: $minutes,
            completed: $completed,
            score: $score
        );
    }

    /**
     * Check if this topic is completed for a specific user (updated)
     */
    public function isCompletedForUser($userId, $enrollmentId = null)
    {
        $progressService = app(ProgressTrackingService::class);

        if ($enrollmentId) {
            $enrollment = CourseEnrollment::find($enrollmentId);
            if ($enrollment) {
                return $progressService->isTopicCompletedForEnrollment($enrollment, $this->id);
            }
        }

        // Fallback to checking progress tracking directly
        return $this->progressTracking()
            ->where('user_id', $userId)
            ->where('is_completed', true)
            ->exists();
    }

    /**
     * Get user's progress details for this topic
     */
    public function getUserProgressDetails(User $user)
    {
        $progress = $this->progressTracking()
            ->where('user_id', $user->id)
            ->first();

        return [
            'is_completed' => $progress ? $progress->is_completed : false,
            'time_spent_minutes' => $progress ? $progress->time_spent_minutes : 0,
            'last_activity' => $progress ? $progress->updated_at : null,
            'completion_score' => $progress ? $progress->completion_score : null,
            'activity_count' => $this->progressTracking()->where('user_id', $user->id)->count(),
        ];
    }
}
