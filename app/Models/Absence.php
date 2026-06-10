<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{

    protected $fillable = [
        'user_id',
        'date',
        'present'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Using user() relation (matches DB schema: user_id)




}

