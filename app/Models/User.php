<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\ClassRoom;
use App\Models\Classe;
use App\Models\Absence;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    
    const ROLE_ADMIN = 'admin';
    const ROLE_PROF = 'prof';
    const ROLE_STUDENT = 'student';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'profile_photo',
        'password',
        'role',
        'subscription_type',
        'payment_date',
        'is_active',
        'test_passed',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
'role' => 'string',
        'test_passed' => 'boolean',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

public function classes()
    {
        return $this->belongsToMany(Classe::class);
    }

public function absences()
{
    return $this->hasMany(Absence::class);
}

public function isAdmin()
{
    return $this->role === static::ROLE_ADMIN;
}

public function isProf()
{
    return $this->role === static::ROLE_PROF;
}

public function isStudent()
{
    return $this->role === static::ROLE_STUDENT;
}

public function results()
{
    return $this->hasMany(\App\Models\Result::class);
}



}
