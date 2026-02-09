<?php
// app/Mail/InactivityReminderEmail.php

namespace App\Mail;

use App\Models\User;
use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InactivityReminderEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public Course $course,
        public int $daysInactive,
        public float $progressPercentage
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📚 Let's Get Back On Track! You've Been Inactive for {$this->daysInactive} Days",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.inactivity-reminder',
            with: [
                'user' => $this->user,
                'course' => $this->course,
                'daysInactive' => $this->daysInactive,
                'progressPercentage' => $this->progressPercentage,
                'nextMilestone' => $this->getNextMilestone(),
                'suggestedActions' => $this->getSuggestedActions(),
                'dueDateInfo' => $this->getDueDateInfo(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Calculate the next milestone based on progress
     */
    private function getNextMilestone(): array
    {
        $milestones = [25, 50, 75, 100];
        $nextMilestone = null;

        foreach ($milestones as $milestone) {
            if ($this->progressPercentage < $milestone) {
                $nextMilestone = $milestone;
                break;
            }
        }

        if ($nextMilestone === null) {
            return [
                'percentage' => 100,
                'text' => 'Course completed!',
                'remaining' => 0,
            ];
        }

        $remaining = $nextMilestone - $this->progressPercentage;

        return [
            'percentage' => $nextMilestone,
            'text' => "{$nextMilestone}% completion",
            'remaining' => round($remaining, 1),
        ];
    }

    /**
     * Get suggested actions based on progress and inactivity
     */
    private function getSuggestedActions(): array
    {
        $actions = [];

        if ($this->progressPercentage < 25) {
            $actions = [
                'Complete the first module to build momentum',
                'Watch the introductory video',
                'Take the first quiz to test your understanding',
            ];
        } elseif ($this->progressPercentage < 50) {
            $actions = [
                'Review the last topic you completed',
                'Try a practice exercise',
                'Join the course discussion forum',
            ];
        } elseif ($this->progressPercentage < 75) {
            $actions = [
                'Work on the mid-course project',
                'Review key concepts from previous modules',
                'Help other students in the forum',
            ];
        } else {
            $actions = [
                'Complete the final assessment',
                'Start working on the capstone project',
                'Review the entire course material',
            ];
        }

        return $actions;
    }

    /**
     * Get due date information if available
     */
    private function getDueDateInfo(): ?array
    {
        if (!$this->course->target_completion_date) {
            return null;
        }

        $now = now();
        $dueDate = $this->course->target_completion_date;

        if ($dueDate->isPast()) {
            $daysOverdue = $now->diffInDays($dueDate);
            return [
                'status' => 'overdue',
                'days' => $daysOverdue,
                'message' => "This course was due {$daysOverdue} " . ($daysOverdue === 1 ? 'day' : 'days') . " ago",
                'urgency' => 'high',
            ];
        }

        $daysRemaining = $now->diffInDays($dueDate, false);

        if ($daysRemaining <= 7) {
            return [
                'status' => 'due_soon',
                'days' => $daysRemaining,
                'message' => "Due in {$daysRemaining} " . ($daysRemaining === 1 ? 'day' : 'days'),
                'urgency' => 'medium',
            ];
        }

        return [
            'status' => 'on_track',
            'days' => $daysRemaining,
            'message' => "Due in {$daysRemaining} days",
            'urgency' => 'low',
        ];
    }
}
