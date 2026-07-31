<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VocalTestSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'vocal_test_prompt_id',
        'subject_id',
        'level_id',
        'class_id',
        'reading_text',
        'test_mode',
        'submission_type',
        'answer_data',
        'auto_correct_count',
        'auto_total_questions',
        'audio_path',
        'audio_original_name',
        'audio_mime_type',
        'audio_size',
        'duration_seconds',
        'status',
        'teacher_comment',
        'score',
        'score_pronunciation',
        'score_tajwid',
        'score_memorization',
        'score_fluency',
        'final_score',
        'submitted_at',
        'reviewed_at',
        'consumed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'duration_seconds' => 'integer',
        'score' => 'integer',
        'score_pronunciation' => 'integer',
        'score_tajwid' => 'integer',
        'score_memorization' => 'integer',
        'score_fluency' => 'integer',
        'final_score' => 'integer',
        'audio_size' => 'integer',
        'answer_data' => 'array',
        'auto_correct_count' => 'integer',
        'auto_total_questions' => 'integer',
        'consumed_at' => 'datetime',
    ];

    const STATUS_SUBMITTED = 'submitted';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_NEEDS_IMPROVEMENT = 'needs_improvement';

    public const TYPE_AUDIO = 'audio';
    public const TYPE_COMPLETION = 'completion';
    public const TYPE_OBSERVATION = 'observation';

    const MODE_READING = 'reading';
    const MODE_TAJWID = 'tajwid';
    const MODE_HIFD = 'hifd';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_SUBMITTED         => 'Soumis',
            self::STATUS_UNDER_REVIEW      => 'En cours d\'évaluation',
            self::STATUS_REVIEWED          => 'Évalué',
            self::STATUS_ACCEPTED          => 'Accepté',
            self::STATUS_NEEDS_IMPROVEMENT => 'À améliorer',
        ];
    }

    public static function getModes(): array
    {
        return [
            self::MODE_READING => 'Lecture',
            self::MODE_TAJWID  => 'Tajwid',
            self::MODE_HIFD    => 'Hifd (Mémorisation)',
        ];
    }

    /**
     * Calcule la note finale comme moyenne des scores disponibles selon le mode.
     */
    public function calculateFinalScore(): ?int
    {
        $scores = [];

        if ($this->score_pronunciation !== null) {
            $scores[] = $this->score_pronunciation;
        }
        if ($this->score_tajwid !== null) {
            $scores[] = $this->score_tajwid;
        }
        if ($this->score_memorization !== null) {
            $scores[] = $this->score_memorization;
        }
        if ($this->score_fluency !== null) {
            $scores[] = $this->score_fluency;
        }

        if (empty($scores)) {
            return $this->score;
        }

        return (int) round(array_sum($scores) / count($scores));
    }

    public function isCompletionSubmission(): bool
    {
        return $this->submission_type === self::TYPE_COMPLETION;
    }

    public function isObservationSubmission(): bool
    {
        return $this->submission_type
            === self::TYPE_OBSERVATION;
    }

    public function observationResponseMode(): ?string
    {
        $value = data_get(
            $this->answer_data,
            'response_mode'
        );

        return is_string($value)
            ? $value
            : null;
    }

    public function observationText(): ?string
    {
        $value = data_get(
            $this->answer_data,
            'observation_text'
        );

        return is_string($value)
            ? trim($value)
            : null;
    }

    public function observationImagePath(): ?string
    {
        $value = data_get(
            $this->answer_data,
            'observation_image.path'
        );

        return is_string($value)
            ? $value
            : null;
    }

    public function observationImageOriginalName(): ?string
    {
        $value = data_get(
            $this->answer_data,
            'observation_image.original_name'
        );

        return is_string($value)
            ? $value
            : null;
    }

    public function observationImageMimeType(): ?string
    {
        $value = data_get(
            $this->answer_data,
            'observation_image.mime_type'
        );

        return is_string($value)
            ? $value
            : null;
    }

    public function observationImageSize(): ?int
    {
        $value = data_get(
            $this->answer_data,
            'observation_image.size'
        );

        return is_numeric($value)
            ? (int) $value
            : null;
    }

    public function completionAnswers(): array
    {
        return (array) data_get($this->answer_data, 'answers', []);
    }

    public function completionExpectedAnswers(): array
    {
        return (array) data_get(
            $this->answer_data,
            'expected_answers',
            []
        );
    }

    public function completionResults(): array
    {
        return (array) data_get($this->answer_data, 'results', []);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(VocalTestPrompt::class, 'vocal_test_prompt_id');
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

    public function appointment(): HasOne
    {
        return $this->hasOne(TestAppointment::class, 'vocal_test_submission_id');
    }
}