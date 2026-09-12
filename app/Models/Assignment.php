<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Assignment extends Model
{
    use HasFactory;

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
        'comment',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    |
    | Compatibilité avec les anciens devoirs utilisant encore les créneaux.
    | Si un cours possède un slot_code mais que class_slot_id n'est pas encore
    | renseigné, Laravel essaie de retrouver automatiquement le créneau.
    |
    | Cette partie peut rester même si les nouvelles interfaces n'utilisent
    | plus directement les créneaux.
    |
    */

    protected static function booted(): void
    {
        static::saving(function (Assignment $assignment) {
            if (
                !Schema::hasColumn(
                    'assignments',
                    'class_slot_id'
                )
                || !Schema::hasTable('class_slots')
                || !Schema::hasColumn(
                    'courses',
                    'slot_code'
                )
                || !empty($assignment->class_slot_id)
                || empty($assignment->course_id)
            ) {
                return;
            }

            $course = Course::query()
                ->find($assignment->course_id);

            if (
                !$course
                || trim((string) $course->slot_code) === ''
            ) {
                return;
            }

            $slot = ClassSlot::query()
                ->where(
                    'subject_id',
                    $course->subject_id
                )
                ->where(
                    'level_id',
                    $course->level_id
                )
                ->where(
                    'class_id',
                    $course->class_id
                )
                ->whereRaw(
                    'UPPER(TRIM(code)) = ?',
                    [
                        strtoupper(
                            trim(
                                (string) $course->slot_code
                            )
                        ),
                    ]
                )
                ->where(
                    'is_active',
                    true
                )
                ->first();

            if ($slot) {
                $assignment->class_slot_id =
                    $slot->id;
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Utilisateur propriétaire du devoir / de la soumission.
     */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * Étudiant lié au devoir.
     *
     * On garde cette relation car certaines parties
     * de l'application utilisent probablement ->student.
     */
    public function student()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * Matière du devoir.
     */
    public function subject()
    {
        return $this->belongsTo(
            Subject::class,
            'subject_id'
        );
    }

    /**
     * Classe du devoir.
     *
     * IMPORTANT :
     * cette relation corrige l'erreur :
     *
     * Call to undefined relationship [classRoom]
     *
     * Le champ dans la table assignments est class_room_id.
     */
    public function classRoom()
    {
        return $this->belongsTo(
            ClassRoom::class,
            'class_room_id'
        );
    }

    /**
     * Cours associé.
     *
     * course_id peut maintenant être NULL.
     */
    public function course()
    {
        return $this->belongsTo(
            Course::class,
            'course_id'
        );
    }

    /**
     * Ancien système de créneaux.
     *
     * On conserve cette relation pour la compatibilité
     * avec les anciennes données.
     */
    public function classSlot()
    {
        return $this->belongsTo(
            ClassSlot::class,
            'class_slot_id'
        );
    }

    /**
     * Relation avec une éventuelle note enregistrée
     * dans la table grades.
     */
    public function grade()
    {
        return $this->hasOne(
            Grade::class,
            'assignment_id'
        );
    }
}