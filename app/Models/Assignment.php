<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Assignment extends Model
{

    protected static function booted(): void
    {
        static::saving(function (Assignment $assignment) {
            if (
                !Schema::hasColumn('assignments', 'class_slot_id')
                || !Schema::hasTable('class_slots')
                || !Schema::hasColumn('courses', 'slot_code')
                || !empty($assignment->class_slot_id)
                || empty($assignment->course_id)
            ) {
                return;
            }

            $course = Course::query()->find($assignment->course_id);

            if (!$course || trim((string) $course->slot_code) === '') {
                return;
            }

            $slot = ClassSlot::query()
                ->where('subject_id', $course->subject_id)
                ->where('level_id', $course->level_id)
                ->where('class_id', $course->class_id)
                ->whereRaw(
                    'UPPER(TRIM(code)) = ?',
                    [strtoupper(trim((string) $course->slot_code))]
                )
                ->where('is_active', true)
                ->first();

            if ($slot) {
                $assignment->class_slot_id = $slot->id;
            }
        });
    }
    protected $fillable = [
        'title',
        'description',
        'file',
        'due_date',
        'course_id',
        'subject_id',
        'class_room_id',
        'class_slot_id',
        'user_id',
        'grade',
        'comment'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function classSlot()
    {
        return $this->belongsTo(
            ClassSlot::class,
            'class_slot_id'
        );
    }

    public function grade()
    {
        return $this->hasOne(Grade::class);
    }

}
