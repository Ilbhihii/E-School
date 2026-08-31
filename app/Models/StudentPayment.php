<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPayment extends Model
{
    use HasFactory;

    public const PLAN_FOUR_MONTHS = 'four_months';
    public const PLAN_ANNUAL = 'annual';

    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'plan_type',
        'amount',
        'paid_at',
        'starts_at',
        'expires_at',
        'payment_method',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
        'starts_at' => 'date',
        'expires_at' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeValidOn($query, $date = null)
    {
        $date = $date ?: now()->toDateString();

        return $query
            ->where('status', self::STATUS_PAID)
            ->whereDate('starts_at', '<=', $date)
            ->whereDate('expires_at', '>=', $date);
    }

    public function isCurrentlyValid(): bool
    {
        if ($this->status !== self::STATUS_PAID) {
            return false;
        }

        $today = Carbon::today();

        return $this->starts_at
            && $this->expires_at
            && $this->starts_at->lte($today)
            && $this->expires_at->gte($today);
    }

    public function getPlanLabelAttribute(): string
    {
        return $this->plan_type === self::PLAN_ANNUAL
            ? 'Année complète'
            : '4 mois';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return [
            'cash' => 'Espèces',
            'bank_transfer' => 'Virement bancaire',
            'paypal' => 'PayPal',
            'other' => 'Autre',
        ][$this->payment_method] ?? 'Non précisé';
    }
}
