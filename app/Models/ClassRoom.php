<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $table = 'class_rooms';

    protected $fillable = [
        'name',
        'level_id',
    ];

    public function level()
    {
        return $this->belongsTo(
            Level::class,
            'level_id'
        );
    }

    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'class_room_subject',
            'class_room_id',
            'subject_id'
        );
    }

    public function courses()
    {
        return $this->hasMany(
            Course::class,
            'class_id'
        );
    }
}
