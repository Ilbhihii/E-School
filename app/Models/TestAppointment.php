<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TestAppointment extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'city',
        'country',
        'type',
        'status',
        'subject_id',
        'level_id',
        'class_id',
        'interview_method',
        'preferred_date',
        'preferred_time',
        'notes',
        'payment_plan',
        'payment_invited_at',
        'payment_invitation_count',
        'vocal_test_submission_id',
        'high_school_test_submission_id',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'payment_invited_at' => 'datetime',
        'payment_invitation_count' => 'integer',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    const TYPE_TEST = 'test';
    const TYPE_INFORMATION = 'information';
    const TYPE_COMMUNICATION = 'communication';
    const TYPE_OTHER = 'other';

    const INTERVIEW_VIDEO = 'video_call';
    const INTERVIEW_PHONE = 'phone_call';
    const INTERVIEW_WHATSAPP = 'whatsapp';

    public static function getTypes(): array
    {
        return [
            self::TYPE_TEST =>
                'Test de niveau / Entretien',
            self::TYPE_INFORMATION =>
                'Prendre des informations',
            self::TYPE_COMMUNICATION =>
                'Communication avec l\'administration',
            self::TYPE_OTHER =>
                'Autre',
        ];
    }

    public static function getInterviewMethods(): array
    {
        return [
            self::INTERVIEW_VIDEO =>
                'Appel vidéo',
            self::INTERVIEW_PHONE =>
                'Appel téléphonique',
            self::INTERVIEW_WHATSAPP =>
                'Message ou appel WhatsApp',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim(
            $this->first_name
            . ' '
            . $this->last_name
        );
    }

    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[
            $this->type
        ] ?? $this->type;
    }

    public function getInterviewMethodLabelAttribute(): string
    {
        return self::getInterviewMethods()[
            $this->interview_method
        ] ?? (
            $this->interview_method
            ?: 'Non précisé'
        );
    }

    public function getPreferredTimeLabelAttribute(): string
    {
        if (!$this->preferred_time) {
            return 'Non précisée';
        }

        return substr(
            (string) $this->preferred_time,
            0,
            5
        );
    }

    public function getPaymentPlanCodeAttribute(): string
    {
        if (
            in_array(
                $this->payment_plan,
                [
                    'premium',
                    'soutien_lycee',
                ],
                true
            )
        ) {
            return $this->payment_plan;
        }

        $subjectName =
            $this->subject?->name
            ?? $this->vocalSubmission?->subject?->name
            ?? $this->highSchoolTestSubmission?->subject?->name
            ?? '';

        $normalized = Str::lower(
            Str::ascii(trim((string) $subjectName))
        );

        return $normalized === 'soutien lycee'
            ? 'soutien_lycee'
            : 'premium';
    }

    public function getPaymentPlanDetailsAttribute(): array
    {
        $planCode = $this->payment_plan_code;

        $configuredPlan = config(
            'plans.offers.' . $planCode
        );

        if (is_array($configuredPlan)) {
            return array_merge(
                [
                    'code' => $planCode,
                    'name' => $planCode === 'soutien_lycee'
                        ? 'Soutien Lycée'
                        : 'Premium',
                    'amount_display' => $planCode === 'soutien_lycee'
                        ? '1000'
                        : '200',
                    'currency_symbol' => $planCode === 'soutien_lycee'
                        ? 'DH'
                        : '€',
                    'period' => 'an',
                    'scope' => $planCode === 'soutien_lycee'
                        ? 'Soutien Lycée uniquement'
                        : 'Tous les parcours',
                ],
                $configuredPlan
            );
        }

        return $planCode === 'soutien_lycee'
            ? [
                'code' => 'soutien_lycee',
                'name' => 'Soutien Lycée',
                'amount_display' => '1000',
                'currency_symbol' => 'DH',
                'period' => 'an',
                'scope' => 'Soutien Lycée uniquement',
            ]
            : [
                'code' => 'premium',
                'name' => 'Premium',
                'amount_display' => '200',
                'currency_symbol' => '€',
                'period' => 'an',
                'scope' => 'Tous les parcours',
            ];
    }

    public function canReceivePaymentInvitation(): bool
    {
        return
            $this->status === self::STATUS_CONFIRMED
            && filter_var(
                $this->email,
                FILTER_VALIDATE_EMAIL
            ) !== false;
    }

    public function scopePending($query)
    {
        return $query->where(
            'status',
            self::STATUS_PENDING
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(
            Subject::class
        );
    }

    public function level()
    {
        return $this->belongsTo(
            Level::class
        );
    }

    public function classRoom()
    {
        return $this->belongsTo(
            ClassRoom::class,
            'class_id'
        );
    }

    public function vocalSubmission()
    {
        return $this->belongsTo(
            VocalTestSubmission::class,
            'vocal_test_submission_id'
        );
    }

    public function highSchoolTestSubmission()
    {
        return $this->belongsTo(
            HighSchoolTestSubmission::class,
            'high_school_test_submission_id'
        );
    }
}
