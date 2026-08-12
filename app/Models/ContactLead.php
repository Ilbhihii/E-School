<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactLead extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_normalized',
        'phone',
        'phone_normalized',
        'country',
        'latest_reason',
        'submissions_count',
        'marketing_consent',
        'first_contact_at',
        'last_contact_at',
        'sheet_synced_at',
    ];

    protected $casts = [
        'submissions_count' => 'integer',
        'marketing_consent' => 'boolean',
        'first_contact_at' => 'datetime',
        'last_contact_at' => 'datetime',
        'sheet_synced_at' => 'datetime',
    ];

    public function requests()
    {
        return $this->hasMany(
            ContactRequest::class,
            'contact_lead_id'
        );
    }

    public function getFullNameAttribute()
    {
        return trim(
            $this->first_name
            . ' '
            . $this->last_name
        );
    }

    public function getIsRepeatedAttribute()
    {
        return $this->submissions_count > 1;
    }
}
