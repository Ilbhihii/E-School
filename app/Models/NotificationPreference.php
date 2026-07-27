<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'new_courses',
        'live_reminders',
        'appointment_updates',
        'progress_updates',
        'vocal_test_feedback',
        'promotional',
    ];

    protected $casts = [
        'new_courses' => 'boolean',
        'live_reminders' => 'boolean',
        'appointment_updates' => 'boolean',
        'progress_updates' => 'boolean',
        'vocal_test_feedback' => 'boolean',
        'promotional' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
