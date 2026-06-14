<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfAssignment extends Model
{
    protected $fillable = [
        'prof_id',
        'level_id',
        'class_id',
        'subject_id',
    ];

    public function prof(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prof_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
