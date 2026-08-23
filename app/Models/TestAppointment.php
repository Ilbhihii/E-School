<?php

namespace App\Models;

use App\Services\PlanCatalogService;
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
        'admission_mode',
        'interview_method',
        'preferred_date',
        'preferred_time',
        'notes',
        'payment_plan',
        'payment_plan_name',
        'payment_duration_months',
        'payment_amount_minor',
        'payment_currency',
        'payment_currency_symbol',
        'payment_invited_at',
        'payment_invitation_count',
        'vocal_test_submission_id',
        'high_school_test_submission_id',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'payment_duration_months' => 'integer',
        'payment_amount_minor' => 'integer',
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

    public function getAdmissionModeLabelAttribute(): string
    {
        $labels = [
            'contact' => 'Prise en contact',
            'vocal_test' => 'Test vocal',
            'test' => 'Test d’admission',
        ];

        return $labels[$this->admission_mode]
            ?? ($this->admission_mode ?: 'Non précisé');
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
        $selected = trim((string) $this->payment_plan);

        /*
         * Pour les nouvelles demandes, on conserve exactement le code
         * choisi par le visiteur, y compris les offres créées depuis
         * l'administration (Family Pack, offres personnalisées, etc.).
         */
        if ($selected !== '') {
            return $selected;
        }

        /*
         * Compatibilité avec les anciennes demandes qui n'avaient pas
         * encore de choix d'offre enregistré.
         */
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
        $catalog = app(PlanCatalogService::class);
        $plan = $catalog->find($planCode, false);

        $durationMonths = (int) (
            $this->payment_duration_months ?: 12
        );

        $pricing = $plan
            ? $catalog->pricingOption(
                $plan,
                $durationMonths
            )
            : null;

        $amountMinor = $this->payment_amount_minor;

        if ($amountMinor === null) {
            $amountMinor = (int) (
                $pricing['amount_minor']
                ?? $plan['amount_minor']
                ?? ($planCode === 'soutien_lycee'
                    ? 100000
                    : 20000)
            );
        }

        $amountMajor = ((int) $amountMinor) / 100;
        $amountDisplay =
            abs($amountMajor - round($amountMajor)) < 0.00001
                ? number_format($amountMajor, 0, ',', ' ')
                : number_format($amountMajor, 2, ',', ' ');

        $durationLabel = $durationMonths === 12
            ? '12 mois — Annuel'
            : ($durationMonths === 1
                ? '1 mois'
                : $durationMonths . ' mois');

        $periodLabel = $durationMonths === 12
            ? 'an'
            : ($durationMonths === 1
                ? 'mois'
                : $durationMonths . ' mois');

        return [
            'code' => $planCode,
            'name' => trim((string) $this->payment_plan_name) !== ''
                ? (string) $this->payment_plan_name
                : (string) (
                    $plan['name']
                    ?? ($planCode === 'soutien_lycee'
                        ? 'Soutien Lycée'
                        : 'Premium')
                ),
            'amount_minor' => (int) $amountMinor,
            'amount_display' => $amountDisplay,
            'currency' => trim((string) $this->payment_currency) !== ''
                ? strtolower((string) $this->payment_currency)
                : strtolower((string) (
                    $plan['currency']
                    ?? ($planCode === 'soutien_lycee'
                        ? 'mad'
                        : 'eur')
                )),
            'currency_symbol' =>
                trim((string) $this->payment_currency_symbol) !== ''
                    ? (string) $this->payment_currency_symbol
                    : (string) (
                        $plan['currency_symbol']
                        ?? ($planCode === 'soutien_lycee'
                            ? 'DH'
                            : '€')
                    ),
            'duration_months' => $durationMonths,
            'duration_label' => $durationLabel,
            'period' => $periodLabel,
            'scope' => (string) (
                $plan['scope']
                ?? ($planCode === 'soutien_lycee'
                    ? 'Soutien Lycée uniquement'
                    : 'Tous les parcours')
            ),
            'is_family_pack' => (bool) (
                $plan['is_family_pack'] ?? false
            ),
            'family_members' => $plan['family_members'] ?? null,
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
