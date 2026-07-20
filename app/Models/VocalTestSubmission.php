<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VocalTestSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'level_id',
        'class_id',
        'recitation_text',
        'audio_path',
        'audio_mime_type',
        'consumed_at',
    ];

    protected $casts = [
        'consumed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function appointment()
    {
        return $this->hasOne(TestAppointment::class);
    }
}
