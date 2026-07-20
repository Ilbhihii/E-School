<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subject_id',
        'user_id',
        'conversation_user_id',
        'message'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function conversationUser()
    {
        return $this->belongsTo(User::class, 'conversation_user_id');
    }

}
