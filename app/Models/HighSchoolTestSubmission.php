<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HighSchoolTestSubmission extends Model
{
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REVISION_REQUESTED =
        'revision_requested';
    public const STATUS_REJECTED = 'rejected';

    /*
     * Ancienne valeur conservée pour les données
     * créées avant l'installation de ce module.
     */
    public const STATUS_REVIEWED = 'reviewed';

    protected $fillable = [
        'user_id',
        'subject_id',
        'level_id',
        'class_id',
        'test_key',
        'test_title',
        'questions_snapshot',
        'answer_images',
        'status',
        'score',
        'teacher_comment',
        'reviewed_by',
        'image_annotations',
        'submitted_at',
        'reviewed_at',
        'access_granted_at',
        'consumed_at',
    ];

    protected $casts = [
        'questions_snapshot' => 'array',
        'answer_images' => 'array',
        'image_annotations' => 'array',
        'score' => 'integer',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'access_granted_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_SUBMITTED =>
                'Soumis',
            self::STATUS_UNDER_REVIEW =>
                'En cours de correction',
            self::STATUS_APPROVED =>
                'Validé',
            self::STATUS_REVISION_REQUESTED =>
                'À refaire',
            self::STATUS_REJECTED =>
                'Refusé',
            self::STATUS_REVIEWED =>
                'Corrigé',
        ];
    }

    public function statusLabel(): string
    {
        return static::statuses()[$this->status]
            ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_REVISION_REQUESTED =>
                'warning',
            self::STATUS_UNDER_REVIEW => 'info',
            self::STATUS_REVIEWED => 'secondary',
            default => 'primary',
        };
    }

    public function isApproved(): bool
    {
        return $this->status
            === self::STATUS_APPROVED;
    }

    public function isPendingReview(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_SUBMITTED,
                self::STATUS_UNDER_REVIEW,
                self::STATUS_REVIEWED,
            ],
            true
        );
    }

    public function canSubmitAgain(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_REVISION_REQUESTED,
                self::STATUS_REJECTED,
            ],
            true
        );
    }

    public function images(): array
    {
        return array_values(
            (array) $this->answer_images
        );
    }

    public function annotations(): array
    {
        return array_values(
            (array) $this->image_annotations
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
        return $this->belongsTo(
            ClassRoom::class,
            'class_id'
        );
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function appointment(): HasOne
    {
        return $this->hasOne(
            TestAppointment::class,
            'high_school_test_submission_id'
        );
    }
}
