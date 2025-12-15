<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SystemNotification extends Model
{
    protected $table = 'system_notifications';

    protected $fillable = [
        'title',
        'message',
        'type',
        'send_email',
        'scheduled_at',
        'sent_at',
        'user_selection',
        'user_ids',
        'role_names',
        'status',
        'data',
    ];

    protected $casts = [
        'send_email' => 'boolean',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'user_ids' => 'array',
        'role_names' => 'array',
        'data' => 'array',
    ];

    protected $appends = ['status_label', 'recipients_count'];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_SENDING = 'sending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    // Type constants
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_ALERT = 'alert';
    const TYPE_SYSTEM = 'system';

    // User selection constants
    const SELECT_ALL = 'all';
    const SELECT_ROLES = 'roles';
    const SELECT_SPECIFIC = 'specific';

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_SENDING => 'Sending',
            self::STATUS_SENT => 'Sent',
            self::STATUS_FAILED => 'Failed',
            default => 'Unknown',
        };
    }

    public function getRecipientsCountAttribute()
    {
        if ($this->user_selection === self::SELECT_ALL) {
            return User::count();
        } elseif ($this->user_selection === self::SELECT_ROLES) {
            return User::role($this->role_names)->count();
        } elseif ($this->user_selection === self::SELECT_SPECIFIC) {
            return count($this->user_ids ?? []);
        }

        return 0;
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class, 'notification_id');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_SCHEDULED => 'blue',
            self::STATUS_SENDING => 'yellow',
            self::STATUS_SENT => 'green',
            self::STATUS_FAILED => 'red',
            default => 'gray',
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
                    ->where('scheduled_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }
}
