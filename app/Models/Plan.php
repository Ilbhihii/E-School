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
        'price_1_month_minor',
        'price_2_month_minor',
        'price_3_month_minor',
        'price_4_month_minor',
        'currency',
        'currency_symbol',
        'period',
        'badge',
        'icon',
        'features',
        'restricted_to_high_school',
        'is_family_pack',
        'family_members',
        'allow_paypal',
        'allow_bank',
        'paypal_url',
        'whatsapp_france',
        'whatsapp_maroc',
        'whatsapp_message',
        'is_recommended',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'amount_minor' => 'integer',
        'price_1_month_minor' => 'integer',
        'price_2_month_minor' => 'integer',
        'price_3_month_minor' => 'integer',
        'price_4_month_minor' => 'integer',
        'features' => 'array',
        'restricted_to_high_school' => 'boolean',
        'is_family_pack' => 'boolean',
        'family_members' => 'integer',
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


    public function getPricingOptionsAttribute()
    {
        $options = [
            $this->pricingOption(12, (int) $this->amount_minor, '12 mois — Annuel', true),
        ];

        foreach ([4, 3, 2, 1] as $months) {
            $column = 'price_' . $months . '_month_minor';
            $minor = $this->{$column};

            if ($minor !== null) {
                $options[] = $this->pricingOption(
                    $months,
                    (int) $minor,
                    $months === 1 ? '1 mois' : $months . ' mois',
                    false
                );
            }
        }

        return $options;
    }

    private function pricingOption($months, $minor, $label, $recommended)
    {
        $amount = ((int) $minor) / 100;
        $display = abs($amount - round($amount)) < 0.00001
            ? number_format($amount, 0, ',', ' ')
            : number_format($amount, 2, ',', ' ');

        return [
            'duration_months' => (int) $months,
            'label' => $label,
            'amount_minor' => (int) $minor,
            'amount_display' => $display,
            'period_label' => $months === 12
                ? 'an'
                : ($months === 1 ? 'mois' : $months . ' mois'),
            'is_best_value' => (bool) $recommended,
        ];
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
            'pricing_options' => $this->pricing_options,
            'currency' => strtolower((string) $this->currency),
            'currency_symbol' => (string) $this->currency_symbol,
            'period' => (string) $this->period,
            'badge' => (string) $this->badge,
            'icon' => $this->icon ?: 'bi-stars',
            'restricted_to_high_school' => (bool) $this->restricted_to_high_school,
            'is_family_pack' => (bool) $this->is_family_pack,
            'family_members' => $this->is_family_pack
                ? max(2, (int) ($this->family_members ?: 4))
                : null,
            'features' => array_values((array) $this->features),
            'allow_paypal' => (bool) $this->allow_paypal,
            'allow_bank' => (bool) $this->allow_bank,
            'paypal_url' => $this->paypal_url,
            'whatsapp_france' => $this->whatsapp_france,
            'whatsapp_maroc' => $this->whatsapp_maroc,
            'whatsapp_message' => $this->whatsapp_message,
            'is_recommended' => (bool) $this->is_recommended,
            'is_active' => (bool) $this->is_active,
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
