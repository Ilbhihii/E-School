<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'level_id',
        'module_id',
        'class_id',
        'slot_code',
        'video',
        'pdf',
        'video_url',
        'order',
        'is_free',
        'course_link',
        'admin_id',
        'user_id',
        'approval_status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function subject()
    {
        return $this->belongsTo(
            Subject::class
        );
    }

    public function classRoom()
    {
        return $this->belongsTo(
            ClassRoom::class,
            'class_id'
        );
    }

    public function level()
    {
        return $this->belongsTo(
            Level::class
        );
    }

    public function module()
    {
        return $this->belongsTo(
            Module::class
        );
    }

    public function admin()
    {
        return $this->belongsTo(
            User::class,
            'admin_id'
        );
    }

    /**
     * Créateur du cours.
     * Peut être l'administration ou un professeur.
     */
    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function reviewer()
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function assignments()
    {
        return $this->hasMany(
            Assignment::class
        );
    }

    public function devoirs()
    {
        return $this->hasMany(
            Assignment::class,
            'course_id'
        );
    }

    public function learningTests()
    {
        return $this->hasMany(
            CourseTest::class
        );
    }

    public function scopeApproved($query)
    {
        return $query->where(
            'approval_status',
            self::STATUS_APPROVED
        );
    }

    public function scopePending($query)
    {
        return $query->where(
            'approval_status',
            self::STATUS_PENDING
        );
    }

    public function scopeRejected($query)
    {
        return $query->where(
            'approval_status',
            self::STATUS_REJECTED
        );
    }

    public function isApproved(): bool
    {
        return $this->approval_status
            === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->approval_status
            === self::STATUS_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->approval_status
            === self::STATUS_REJECTED;
    }

    public function wasSubmittedByProfessor(): bool
    {
        return
            $this->creator
            && $this->creator->isProf();
    }
}
