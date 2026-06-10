<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTest extends Model
{
    use HasFactory;

    protected $table = 'course_tests';

    protected $fillable = [
        'course_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'correct_answer'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

