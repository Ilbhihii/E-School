<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    public $timestamps = false;
    // note: column renamed from class_room_id to class_id for consistency
    protected $fillable = ['name', 'type', 'description', 'image'];

    public function levels()
    {
        return $this->hasMany(Level::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function classes()
    {
        return $this->belongsToMany(
            ClassRoom::class,
            'class_room_subject',
            'subject_id',
            'class_room_id'
        );
    }

    public function highSchoolTestSubmissions()
    {
        return $this->hasMany(
            HighSchoolTestSubmission::class
        );
    }

}

