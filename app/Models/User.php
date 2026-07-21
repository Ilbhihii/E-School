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
use App\Notifications\ResetPasswordNotification;

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
        'is_paid',
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
        'is_paid' => 'boolean',
'role' => 'string',
        'test_passed' => 'boolean',
    ];

    /**
     * Envoie l'email personnalisé de réinitialisation du mot de passe.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

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

/**
 * Retourne la route du tableau de bord correspondant au rôle du compte.
 */
public function dashboardRoute(): string
{
    return match ($this->role) {
        static::ROLE_ADMIN => route('admin.dashboard'),
        static::ROLE_PROF => route('prof.dashboard'),
        static::ROLE_STUDENT => route('student.dashboard'),
        default => route('home'),
    };
}

public function results()
    {
        return $this->hasMany(\App\Models\Result::class);
    }

    /**
     * Récupère les matières assignées individuellement à l'étudiant via class_user.subject_id.
     */
    public function individuallyAssignedSubjects()
    {
        $subjectIds = \DB::table('class_user')
            ->where('user_id', $this->id)
            ->whereNotNull('subject_id')
            ->pluck('subject_id')
            ->unique();

        if ($subjectIds->isNotEmpty()) {
            return \App\Models\Subject::whereIn('id', $subjectIds)->get();
        }

        return collect();
    }

}
