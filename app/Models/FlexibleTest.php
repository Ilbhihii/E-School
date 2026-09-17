<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlexibleTest extends Model
{
    public const SOURCE_TEXT = 'text';
    public const SOURCE_FILES = 'files';
    public const SOURCE_MIXED = 'mixed';

    public const RESPONSE_VOCAL = 'vocal';
    public const RESPONSE_WRITTEN = 'written';
    public const RESPONSE_QCM = 'qcm';

    protected $fillable = [
        'subject_id',
        'level_id',
        'class_id',
        'title',
        'instructions',
        'source_type',
        'source_text',
        'source_files',
        'response_type',
        'written_response_mode',
        'vocal_mode',
        'qcm_questions',
        'preparation_seconds',
        'maximum_duration',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'source_files' => 'array',
        'qcm_questions' => 'array',
        'preparation_seconds' => 'integer',
        'maximum_duration' => 'integer',
        'is_active' => 'boolean',
    ];

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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}