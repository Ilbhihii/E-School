<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VocalTestPrompt extends Model
{
    protected $fillable = [
        'subject_id',
        'level_id',
        'class_id',
        'title',
        'instructions',
        'reading_text',
        'test_mode',
        'preparation_seconds',
        'maximum_duration',
        'hide_text_during_recording',
        'is_active',
    ];

    protected $casts = [
        'preparation_seconds' => 'integer',
        'maximum_duration' => 'integer',
        'hide_text_during_recording' => 'boolean',
        'is_active' => 'boolean',
    ];

    const MODE_READING = 'reading';
    const MODE_TAJWID = 'tajwid';
    const MODE_HIFD = 'hifd';

    public static function getModes(): array
    {
        return [
            self::MODE_READING => 'Lecture',
            self::MODE_TAJWID  => 'Tajwid',
            self::MODE_HIFD    => 'Hifd (Mémorisation)',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(VocalTestSubmission::class, 'vocal_test_prompt_id');
    }
}
