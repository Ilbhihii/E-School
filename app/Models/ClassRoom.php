<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\Models\Live;
use App\Models\User;

class ClassRoom extends Model
{
    use HasFactory;

protected $table = 'class_rooms';

protected $fillable = ['name', 'level_id'];

    // 📘 Une classe a plusieurs cours
    public function courses()
    {
        return $this->hasMany(Course::class, 'class_id');
    }


    // 🔴 Une classe a plusieurs lives
    public function lives()
    {
        return $this->hasMany(Live::class, 'class_id');
    }

    // 👥 Une classe a plusieurs utilisateurs
    public function users()
    {
        return $this->hasMany(User::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_user', 'class_id', 'user_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

}
