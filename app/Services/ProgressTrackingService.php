<?php
// app/Services/ProgressTrackingService.php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\CourseOutline;
use App\Models\ProgressTracking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProgressTrackingService
{
    public function recordActivity(User $user, Course $course, $outlineId, string $activityType, float $minutes, bool $completed = false, $score = null): ProgressTracking
    {
        $progress = ProgressTracking::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'course_outline_id' => $outlineId,
            'activity_type' => $activityType,
        ]);

        $progress->recordActivity($minutes, $completed, $score);

        // Update topic completion if marked as completed
        if ($completed && $outlineId) {
            $outline = CourseOutline::find($outlineId);
            if ($outline && !$outline->is_completed) {
                $outline->markAsCompleted();
            }
        }

        // Update course and module progress
        $course->updateProgress();
        if ($outlineId) {
            $outline = CourseOutline::with('module')->find($outlineId);
            if ($outline && $outline->module) {
                $outline->module->course->updateProgress();
            }
        }

        return $progress;
    }

    public function getUserProgress(User $user, Course $course = null): Collection
    {
        $query = ProgressTracking::with(['course', 'courseOutline.module'])
            ->where('user_id', $user->id);

        if ($course) {
            $query->where('course_id', $course->id);
        }

        return $query->get()->groupBy('course_id');
    }

    public function calculateCourseProgress(Course $course): array
    {
        // Get all modules and their topics for this course
        $modules = $course->modules()->with('topics')->get();

        $totalModules = $modules->count();
        $completedModules = $modules->where('is_completed', true)->count();

        $totalTopics = 0;
        $completedTopics = 0;
        $totalEstimatedMinutes = 0;
        $completedTimeMinutes = 0;

        foreach ($modules as $module) {
            $totalTopics += $module->topics->count();
            $completedTopics += $module->topics->where('is_completed', true)->count();
            $totalEstimatedMinutes += $module->estimated_duration_minutes;

            // Calculate actual time spent on this module's topics
            $moduleTopicsIds = $module->topics->pluck('id');
            $completedTimeMinutes += ProgressTracking::whereIn('course_outline_id', $moduleTopicsIds)
                ->sum('time_spent_minutes');
        }

        $totalTime = $course->progressTracking()->sum('time_spent_minutes');
        $averageScore = $course->progressTracking()->where('completion_score', '>', 0)->avg('completion_score');

        return [
            'completed_modules' => $completedModules,
            'total_modules' => $totalModules,
            'completed_topics' => $completedTopics,
            'total_topics' => $totalTopics,
            'module_completion_percentage' => $totalModules > 0 ? ($completedModules / $totalModules) * 100 : 0,
            'topic_completion_percentage' => $totalTopics > 0 ? ($completedTopics / $totalTopics) * 100 : 0,
            'overall_completion_percentage' => $this->calculateOverallCompletion($completedModules, $totalModules, $completedTopics, $totalTopics),
            'total_time_minutes' => $totalTime,
            'completed_time_minutes' => $completedTimeMinutes,
            'estimated_total_minutes' => $totalEstimatedMinutes,
            'average_score' => round($averageScore, 1),
            'estimated_remaining_time' => $this->calculateRemainingTime($completedTimeMinutes, $totalEstimatedMinutes, $completedTopics, $totalTopics),
            'progress_by_module' => $this->getModuleProgress($modules),
        ];
    }

    protected function calculateOverallCompletion(int $completedModules, int $totalModules, int $completedTopics, int $totalTopics): float
    {
        // Weight modules 40% and topics 60% for overall completion
        $moduleWeight = 0.4;
        $topicWeight = 0.6;

        $moduleCompletion = $totalModules > 0 ? ($completedModules / $totalModules) * 100 : 0;
        $topicCompletion = $totalTopics > 0 ? ($completedTopics / $totalTopics) * 100 : 0;

        return ($moduleCompletion * $moduleWeight) + ($topicCompletion * $topicWeight);
    }

    protected function calculateRemainingTime(float $completedTime, float $totalEstimated, int $completedTopics, int $totalTopics): int
    {
        if ($completedTopics === 0) return (int) $totalEstimated;

        if ($completedTime > 0) {
            // Calculate based on actual pace
            $timePerTopic = $completedTime / $completedTopics;
            $remainingTopics = $totalTopics - $completedTopics;
            return (int) ($timePerTopic * $remainingTopics);
        } else {
            // Fallback to estimated time
            $estimatedPerTopic = $totalEstimated / $totalTopics;
            $remainingTopics = $totalTopics - $completedTopics;
            return (int) ($estimatedPerTopic * $remainingTopics);
        }
    }

    protected function getModuleProgress(Collection $modules): array
    {
        $progressByModule = [];

        foreach ($modules as $module) {
            $totalTopics = $module->topics->count();
            $completedTopics = $module->topics->where('is_completed', true)->count();

            $moduleTime = ProgressTracking::whereIn('course_outline_id', $module->topics->pluck('id'))
                ->sum('time_spent_minutes');

            $progressByModule[] = [
                'module_id' => $module->id,
                'module_title' => $module->title,
                'completed_topics' => $completedTopics,
                'total_topics' => $totalTopics,
                'completion_percentage' => $totalTopics > 0 ? ($completedTopics / $totalTopics) * 100 : 0,
                'time_spent_minutes' => $moduleTime,
                'is_completed' => $module->is_completed,
                'estimated_duration_minutes' => $module->estimated_duration_minutes,
            ];
        }

        return $progressByModule;
    }

    public function getLearningAnalytics(User $user): array
    {
        $progressData = $this->getUserProgress($user);

        $totalStudyTime = $progressData->flatten()->sum('time_spent_minutes');
        $completedItems = $progressData->flatten()->where('is_completed', true)->count();
        $averageScores = $progressData->flatten()->where('completion_score', '>', 0)->avg('completion_score');

        $weeklyProgress = $this->getWeeklyProgress($user);
        $courseProgress = $this->getCourseProgressSummary($user);

        return [
            'total_study_time_hours' => round($totalStudyTime / 60, 1),
            'completed_items' => $completedItems,
            'completed_topics' => $completedItems, // alias for backward compatibility
            'average_score' => round($averageScores, 1),
            'active_courses' => $progressData->count(),
            'weekly_progress' => $weeklyProgress,
            'streak_days' => $this->calculateStreak($user),
            'course_progress' => $courseProgress,
            'efficiency_metrics' => $this->calculateEfficiencyMetrics($user, $totalStudyTime, $completedItems),
        ];
    }

    protected function getCourseProgressSummary(User $user): array
    {
        $courses = Course::where('student_profile_id', $user->studentProfile->id ?? null)
            ->with(['modules.topics'])
            ->get();

        $summary = [];

        foreach ($courses as $course) {
            $progress = $this->calculateCourseProgress($course);
            $summary[] = [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'overall_completion' => $progress['overall_completion_percentage'],
                'module_completion' => $progress['module_completion_percentage'],
                'topic_completion' => $progress['topic_completion_percentage'],
                'time_spent_hours' => round($progress['total_time_minutes'] / 60, 1),
                'estimated_remaining_hours' => round($progress['estimated_remaining_time'] / 60, 1),
            ];
        }

        return $summary;
    }

    protected function getWeeklyProgress(User $user): array
    {
        $startDate = now()->subDays(6)->startOfDay();

        return ProgressTracking::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(time_spent_minutes) as study_time, COUNT(*) as activities')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => [
                    'study_time_minutes' => $item->study_time,
                    'study_time_hours' => round($item->study_time / 60, 1),
                    'activities' => $item->activities
                ]];
            })
            ->toArray();
    }

    protected function calculateStreak(User $user): int
    {
        $dates = ProgressTracking::where('user_id', $user->id)
            ->selectRaw('DATE(created_at) as date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date');

        $streak = 0;
        $currentDate = now()->startOfDay();

        foreach ($dates as $dateString) {
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $dateString)->startOfDay();

            // Check if this date matches the current streak day
            if ($date->equalTo($currentDate)) {
                $streak++;
                $currentDate->subDay();
            } else {
                // If there's a gap in dates, break the streak
                break;
            }
        }

        return $streak;
    }

    protected function calculateEfficiencyMetrics(User $user, float $totalStudyTime, int $completedItems): array
    {
        if ($completedItems === 0) {
            return [
                'minutes_per_item' => 0,
                'completion_rate' => 0,
                'efficiency_score' => 0,
            ];
        }

        $minutesPerItem = $totalStudyTime / $completedItems;

        // Calculate completion rate (items per study session)
        $studySessions = ProgressTracking::where('user_id', $user->id)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as items_completed')
            ->groupBy('date')
            ->get();

        $completionRate = $studySessions->avg('items_completed') ?? 0;

        // Simple efficiency score (higher is better)
        $efficiencyScore = min(100, ($completedItems / max(1, $totalStudyTime / 60)) * 10);

        return [
            'minutes_per_item' => round($minutesPerItem, 1),
            'completion_rate' => round($completionRate, 1),
            'efficiency_score' => round($efficiencyScore, 1),
        ];
    }

    /**
     * Mark a module as completed based on its topics
     */
    public function updateModuleCompletion(Module $module): void
    {
        $totalTopics = $module->topics->count();
        $completedTopics = $module->topics->where('is_completed', true)->count();

        if ($totalTopics > 0 && $completedTopics === $totalTopics && !$module->is_completed) {
            $module->markAsCompleted();
        } elseif ($completedTopics < $totalTopics && $module->is_completed) {
            $module->markAsIncomplete();
        }
    }

    /**
     * Get detailed progress for a specific module
     */
    public function getModuleProgressDetail(Module $module, User $user): array
    {
        $topicsProgress = [];

        foreach ($module->topics as $topic) {
            $progress = ProgressTracking::where('user_id', $user->id)
                ->where('course_outline_id', $topic->id)
                ->first();

            $topicsProgress[] = [
                'topic_id' => $topic->id,
                'topic_title' => $topic->title,
                'is_completed' => $topic->is_completed,
                'time_spent_minutes' => $progress->time_spent_minutes ?? 0,
                'last_activity' => $progress->updated_at ?? null,
                'completion_score' => $progress->completion_score ?? null,
            ];
        }

        return [
            'module_id' => $module->id,
            'module_title' => $module->title,
            'total_topics' => $module->topics->count(),
            'completed_topics' => $module->topics->where('is_completed', true)->count(),
            'completion_percentage' => $module->topics->count() > 0 ?
                ($module->topics->where('is_completed', true)->count() / $module->topics->count()) * 100 : 0,
            'topics_progress' => $topicsProgress,
            'total_time_spent_minutes' => array_sum(array_column($topicsProgress, 'time_spent_minutes')),
        ];
    }
}
