<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    protected $fillable = [
        'contact_lead_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'reason',
        'marketing_consent',
        'source',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'marketing_consent' => 'boolean',
    ];

    public function lead()
    {
        return $this->belongsTo(
            ContactLead::class,
            'contact_lead_id'
        );
    }
}
