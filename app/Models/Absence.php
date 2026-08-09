<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'level_id',
        'class_id',
        'class_slot_id',
        'date',
        'present',
    ];

    protected $casts = [
        'class_slot_id' => 'integer',
        'date' => 'date',
        'present' => 'boolean',
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

    public function classSlot()
    {
        return $this->belongsTo(
            ClassSlot::class,
            'class_slot_id'
        );
    }
}
