<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Assignment;
use App\Models\User;
use App\Models\Level;
use App\Models\CourseTest;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'level_id',
        'module_id',
        'class_id',
        'video',
        'pdf',
        'video_url',
        'order',
        'is_free',
        'course_link',
        'admin_id',
        'user_id'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function devoirs()
    {
        return $this->hasMany(Assignment::class, 'course_id');
    }

    public function learningTests()
    {
        return $this->hasMany(CourseTest::class);
    }
}
