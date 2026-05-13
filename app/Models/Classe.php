<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Classe extends Model
{
    protected $fillable = ['name'];

// Relation many-to-many avec User
    public function students()
    {
        return $this->belongsToMany(User::class, 'classe_user', 'class_id', 'user_id');
    }

}
