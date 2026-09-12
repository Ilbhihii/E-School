<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HomeworkSubmissionService
{
    /**
     * Vérifie si l'étudiant a remis le devoir du professeur.
     *
     * La table assignments contient historiquement à la fois les devoirs
     * créés par les professeurs et les fichiers remis par les étudiants.
     * On reprend donc les mêmes règles que l'espace étudiant :
     * cours identique, titre correspondant, ou remise dans la fenêtre
     * temporelle du devoir jusqu'au devoir suivant du même parcours.
     */
    public function hasSubmitted(
        Assignment $teacherAssignment,
        int $studentId
    ): bool {
        $query = Assignment::query()
            ->where('user_id', $studentId)
            ->where('subject_id', $teacherAssignment->subject_id)
            ->where('class_room_id', $teacherAssignment->class_room_id)
            ->where('created_at', '>=', $teacherAssignment->created_at);

        $nextAssignment = Assignment::query()
            ->where('user_id', $teacherAssignment->user_id)
            ->where('subject_id', $teacherAssignment->subject_id)
            ->where('class_room_id', $teacherAssignment->class_room_id)
            ->where('id', '!=', $teacherAssignment->id)
            ->where('created_at', '>', $teacherAssignment->created_at)
            ->orderBy('created_at')
            ->first();

        if ($nextAssignment) {
            $query->where('created_at', '<', $nextAssignment->created_at);
        }

        $submissions = $query
            ->orderBy('created_at')
            ->get();

        foreach ($submissions as $submission) {
            if (
                $teacherAssignment->class_slot_id
                && $submission->class_slot_id
                && (int) $teacherAssignment->class_slot_id
                    !== (int) $submission->class_slot_id
            ) {
                continue;
            }

            if (
                $teacherAssignment->course_id
                && $submission->course_id
                && (int) $teacherAssignment->course_id
                    === (int) $submission->course_id
            ) {
                return true;
            }

            $teacherTitle = trim((string) $teacherAssignment->title);

            if (
                $teacherTitle !== ''
                && mb_stripos(
                    (string) $submission->title,
                    $teacherTitle
                ) !== false
            ) {
                return true;
            }

            // Compatibilité avec les remises dont le titre a été saisi librement.
            // La fenêtre temporelle est bornée par le devoir suivant afin qu'une
            // même remise ne valide pas plusieurs devoirs successifs.
            return true;
        }

        return false;
    }

    public function studentsForAssignment(
        Assignment $assignment
    ) {
        $ids = DB::table('class_user')
            ->where('subject_id', $assignment->subject_id)
            ->where('class_id', $assignment->class_room_id)
            ->pluck('user_id')
            ->unique()
            ->values();

        return User::query()
            ->where('role', User::ROLE_STUDENT)
            ->whereIn('id', $ids)
            ->where(function ($query) {
                $query->whereNull('is_active')
                    ->orWhere('is_active', true);
            })
            ->orderBy('name')
            ->get();
    }
}
