<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSlot extends Model
{
    protected $fillable = [
        'subject_id',
        'level_id',
        'class_id',
        'code',
        'position',
        'is_active',
    ];

    protected $casts = [
        'subject_id' => 'integer',
        'level_id' => 'integer',
        'class_id' => 'integer',
        'position' => 'integer',
        'is_active' => 'boolean',
    ];

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
        return $this->belongsTo(
            ClassRoom::class,
            'class_id'
        );
    }

    public function getDisplayLabelAttribute(): string
    {
        return strtoupper(trim((string) $this->code));
    }
}
