<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    // note: column renamed from class_room_id to class_id for consistency
    protected $fillable = ['name', 'type'];

    // Removed conflicting belongsTo - using belongsToMany via pivot instead

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

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function classes()
    {
        return $this->belongsToMany(ClassRoom::class);
    }

}

