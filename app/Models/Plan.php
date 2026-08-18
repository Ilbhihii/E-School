<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'subtitle',
        'scope',
        'amount_minor',
        'currency',
        'currency_symbol',
        'period',
        'badge',
        'icon',
        'features',
        'restricted_to_high_school',
        'allow_paypal',
        'allow_bank',
        'paypal_url',
        'is_recommended',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'amount_minor' => 'integer',
        'features' => 'array',
        'restricted_to_high_school' => 'boolean',
        'allow_paypal' => 'boolean',
        'allow_bank' => 'boolean',
        'is_recommended' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function getAmountDisplayAttribute()
    {
        $amount = ((int) $this->amount_minor) / 100;

        if (abs($amount - round($amount)) < 0.00001) {
            return number_format($amount, 0, ',', ' ');
        }

        return number_format($amount, 2, ',', ' ');
    }

    public function getAmountMajorAttribute()
    {
        return number_format(
            ((int) $this->amount_minor) / 100,
            2,
            '.',
            ''
        );
    }

    public function isSystemPlan()
    {
        return in_array(
            $this->code,
            ['premium', 'soutien_lycee'],
            true
        );
    }

    public function toCatalogArray()
    {
        return [
            'id' => (int) $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'subtitle' => (string) $this->subtitle,
            'scope' => (string) $this->scope,
            'amount_display' => $this->amount_display,
            'amount_minor' => (int) $this->amount_minor,
            'currency' => strtolower((string) $this->currency),
            'currency_symbol' => (string) $this->currency_symbol,
            'period' => (string) $this->period,
            'badge' => (string) $this->badge,
            'icon' => $this->icon ?: 'bi-stars',
            'restricted_to_high_school' => (bool) $this->restricted_to_high_school,
            'features' => array_values((array) $this->features),
            'allow_paypal' => (bool) $this->allow_paypal,
            'allow_bank' => (bool) $this->allow_bank,
            'paypal_url' => $this->paypal_url,
            'is_recommended' => (bool) $this->is_recommended,
            'is_active' => (bool) $this->is_active,
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
