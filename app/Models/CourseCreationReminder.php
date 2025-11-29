<?php
// app/Models/CourseCreationReminder.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCreationReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reminder_count',
        'last_reminder_sent_at',
        'completed_at',
    ];

    protected $casts = [
        'last_reminder_sent_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canSendReminder(): bool
    {
        // Don't send if already completed or reached max reminders
        if ($this->completed_at || $this->reminder_count >= 3) {
            return false;
        }

        // Check if 48 hours have passed since last reminder
        if ($this->last_reminder_sent_at) {
            return $this->last_reminder_sent_at->diffInHours(now()) >= 48;
        }

        // First reminder can be sent immediately
        return true;
    }

    public function markReminderSent()
    {
        $this->update([
            'reminder_count' => $this->reminder_count + 1,
            'last_reminder_sent_at' => now(),
            'completed_at' => $this->reminder_count + 1 >= 3 ? now() : null,
        ]);
    }

    public function markCompleted()
    {
        $this->update([
            'completed_at' => now(),
        ]);
    }
}