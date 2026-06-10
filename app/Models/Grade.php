<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'grade',
        'comment'
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

}
