<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClassRoom;


class Live extends Model
{
    use HasFactory;

protected $fillable = [
        'title',
        'class_id',
        'stream_url',
        'admin_id',
        'user_id',
        'live_date',
        'start_time',
        'end_time',
    ];




    // Relation vers la classe
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
