<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentReminder extends Model
{
    public const KIND_STUDENT_MISSING = 'student_missing';
    public const KIND_PARENT_MULTIPLE = 'parent_multiple_missing';

    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SKIPPED = 'skipped';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'recipient_user_id',
        'kind',
        'channel',
        'recipient',
        'missing_count',
        'status',
        'error_message',
        'meta',
        'sent_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'sent_at' => 'datetime',
        'missing_count' => 'integer',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function recipientUser()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }
}
