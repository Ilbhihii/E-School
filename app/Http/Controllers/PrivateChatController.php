<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PrivateChatController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PROFESSEUR
    |--------------------------------------------------------------------------
    */

    public function profIndex()
    {
        $professor = auth()->user();

        abort_unless(
            $professor && $professor->role === 'prof',
            403
        );

        $contacts = $this
            ->studentsForProfessor($professor->id)
            ->map(function ($contact) use ($professor) {
                $lastMessage = Message::query()
                    ->where(
                        'subject_id',
                        $contact->subject_id
                    )
                    ->where(
                        'conversation_user_id',
                        $contact->student_id
                    )
                    ->where(
                        'private_professor_id',
                        $professor->id
                    )
                    ->latest('created_at')
                    ->first();

                $contact->last_message =
                    $lastMessage;

                return $contact;
            })
            ->sortByDesc(
                fn ($contact) =>
                    optional(
                        $contact->last_message
                    )->created_at?->timestamp ?? 0
            )
            ->values();

        return view(
            'prof.private-chats.index',
            compact('contacts')
        );
    }

    public function profShow(
        User $student,
        Subject $subject
    ) {
        $professor = auth()->user();

        abort_unless(
            $professor && $professor->role === 'prof',
            403
        );

        abort_unless(
            $student->role === 'student',
            404
        );

        $path = $this->professorStudentPath(
            $professor->id,
            $student->id,
            $subject->id
        );

        abort_unless(
            $path !== null,
            403,
            'Cet étudiant ne fait pas partie de vos classes assignées.'
        );

        $messages = Message::query()
            ->with('user')
            ->where(
                'subject_id',
                $subject->id
            )
            ->where(
                'conversation_user_id',
                $student->id
            )
            ->where(
                'private_professor_id',
                $professor->id
            )
            ->orderBy('created_at', 'asc')
            ->get();

        return view(
            'prof.private-chats.show',
            compact(
                'student',
                'subject',
                'path',
                'messages'
            )
        );
    }

    public function profSend(
        Request $request,
        User $student,
        Subject $subject
    ) {
        $professor = auth()->user();

        abort_unless(
            $professor && $professor->role === 'prof',
            403
        );

        abort_unless(
            $student->role === 'student',
            404
        );

        abort_unless(
            $this->professorStudentPath(
                $professor->id,
                $student->id,
                $subject->id
            ) !== null,
            403,
            'Cet étudiant ne fait pas partie de vos classes assignées.'
        );

        $validated = $request->validate([
            'message' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        Message::create([
            'user_id' =>
                $professor->id,
            'subject_id' =>
                $subject->id,
            'conversation_user_id' =>
                $student->id,
            'private_professor_id' =>
                $professor->id,
            'message' =>
                trim($validated['message']),
        ]);

        return back()->with(
            'success',
            'Message envoyé.'
        );
    }

    public function profDelete(
        Request $request,
        User $student,
        Subject $subject
    ) {
        $professor = auth()->user();

        abort_unless(
            $professor && $professor->role === 'prof',
            403
        );

        abort_unless(
            $this->professorStudentPath(
                $professor->id,
                $student->id,
                $subject->id
            ) !== null,
            403
        );

        $validated = $request->validate([
            'message_id' => [
                'required',
                'integer',
                'exists:messages,id',
            ],
        ]);

        $message = Message::query()
            ->whereKey(
                $validated['message_id']
            )
            ->where(
                'user_id',
                $professor->id
            )
            ->where(
                'subject_id',
                $subject->id
            )
            ->where(
                'conversation_user_id',
                $student->id
            )
            ->where(
                'private_professor_id',
                $professor->id
            )
            ->firstOrFail();

        $message->delete();

        return back()->with(
            'success',
            'Message supprimé.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ÉTUDIANT
    |--------------------------------------------------------------------------
    */

    public function studentIndex()
    {
        $student = auth()->user();

        abort_unless(
            $student && $student->role === 'student',
            403
        );

        $contacts = $this
            ->professorsForStudent($student->id)
            ->map(function ($contact) use ($student) {
                $lastMessage = Message::query()
                    ->where(
                        'subject_id',
                        $contact->subject_id
                    )
                    ->where(
                        'conversation_user_id',
                        $student->id
                    )
                    ->where(
                        'private_professor_id',
                        $contact->professor_id
                    )
                    ->latest('created_at')
                    ->first();

                $contact->last_message =
                    $lastMessage;

                return $contact;
            })
            ->sortByDesc(
                fn ($contact) =>
                    optional(
                        $contact->last_message
                    )->created_at?->timestamp ?? 0
            )
            ->values();

        return view(
            'student.private-chats.index',
            compact('contacts')
        );
    }

    public function studentShow(
        User $professor,
        Subject $subject
    ) {
        $student = auth()->user();

        abort_unless(
            $student && $student->role === 'student',
            403
        );

        abort_unless(
            $professor->role === 'prof',
            404
        );

        $path = $this->professorStudentPath(
            $professor->id,
            $student->id,
            $subject->id
        );

        abort_unless(
            $path !== null,
            403,
            'Ce professeur n’est pas affecté à votre parcours.'
        );

        $messages = Message::query()
            ->with('user')
            ->where(
                'subject_id',
                $subject->id
            )
            ->where(
                'conversation_user_id',
                $student->id
            )
            ->where(
                'private_professor_id',
                $professor->id
            )
            ->orderBy('created_at', 'asc')
            ->get();

        return view(
            'student.private-chats.show',
            compact(
                'professor',
                'subject',
                'path',
                'messages'
            )
        );
    }

    public function studentSend(
        Request $request,
        User $professor,
        Subject $subject
    ) {
        $student = auth()->user();

        abort_unless(
            $student && $student->role === 'student',
            403
        );

        abort_unless(
            $professor->role === 'prof',
            404
        );

        abort_unless(
            $this->professorStudentPath(
                $professor->id,
                $student->id,
                $subject->id
            ) !== null,
            403,
            'Ce professeur n’est pas affecté à votre parcours.'
        );

        $validated = $request->validate([
            'message' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        Message::create([
            'user_id' =>
                $student->id,
            'subject_id' =>
                $subject->id,
            'conversation_user_id' =>
                $student->id,
            'private_professor_id' =>
                $professor->id,
            'message' =>
                trim($validated['message']),
        ]);

        return back()->with(
            'success',
            'Message envoyé.'
        );
    }

    public function studentDelete(
        Request $request,
        User $professor,
        Subject $subject
    ) {
        $student = auth()->user();

        abort_unless(
            $student && $student->role === 'student',
            403
        );

        abort_unless(
            $this->professorStudentPath(
                $professor->id,
                $student->id,
                $subject->id
            ) !== null,
            403
        );

        $validated = $request->validate([
            'message_id' => [
                'required',
                'integer',
                'exists:messages,id',
            ],
        ]);

        $message = Message::query()
            ->whereKey(
                $validated['message_id']
            )
            ->where(
                'user_id',
                $student->id
            )
            ->where(
                'subject_id',
                $subject->id
            )
            ->where(
                'conversation_user_id',
                $student->id
            )
            ->where(
                'private_professor_id',
                $professor->id
            )
            ->firstOrFail();

        $message->delete();

        return back()->with(
            'success',
            'Message supprimé.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SÉCURITÉ / PARCOURS
    |--------------------------------------------------------------------------
    */

    /**
     * Un professeur peut contacter uniquement les étudiants
     * inscrits dans une classe + matière qui lui sont affectées.
     */
    private function professorStudentPath(
        int $professorId,
        int $studentId,
        int $subjectId
    ): ?object {
        return DB::table(
                'prof_assignments as pa'
            )
            ->join(
                'class_user as cu',
                function ($join) {
                    $join
                        ->on(
                            'cu.subject_id',
                            '=',
                            'pa.subject_id'
                        )
                        ->on(
                            'cu.class_id',
                            '=',
                            'pa.class_id'
                        );
                }
            )
            ->join(
                'class_rooms as cr',
                'cr.id',
                '=',
                'pa.class_id'
            )
            ->join(
                'levels as l',
                'l.id',
                '=',
                'pa.level_id'
            )
            ->join(
                'subjects as s',
                's.id',
                '=',
                'pa.subject_id'
            )
            ->where(
                'pa.prof_id',
                $professorId
            )
            ->where(
                'cu.user_id',
                $studentId
            )
            ->where(
                'pa.subject_id',
                $subjectId
            )
            ->whereColumn(
                'cr.level_id',
                'pa.level_id'
            )
            ->select([
                's.id as subject_id',
                's.name as subject_name',
                'l.id as level_id',
                'l.name as level_name',
                'cr.id as class_id',
                'cr.name as class_name',
            ])
            ->first();
    }

    /**
     * Liste des étudiants accessibles au professeur,
     * sans exposer les autres comptes de la plateforme.
     */
    private function studentsForProfessor(
        int $professorId
    ): Collection {
        return DB::table(
                'prof_assignments as pa'
            )
            ->join(
                'class_user as cu',
                function ($join) {
                    $join
                        ->on(
                            'cu.subject_id',
                            '=',
                            'pa.subject_id'
                        )
                        ->on(
                            'cu.class_id',
                            '=',
                            'pa.class_id'
                        );
                }
            )
            ->join(
                'users as student',
                'student.id',
                '=',
                'cu.user_id'
            )
            ->join(
                'subjects as s',
                's.id',
                '=',
                'pa.subject_id'
            )
            ->join(
                'class_rooms as cr',
                'cr.id',
                '=',
                'pa.class_id'
            )
            ->join(
                'levels as l',
                'l.id',
                '=',
                'pa.level_id'
            )
            ->where(
                'pa.prof_id',
                $professorId
            )
            ->where(
                'student.role',
                'student'
            )
            ->whereColumn(
                'cr.level_id',
                'pa.level_id'
            )
            ->select([
                'student.id as student_id',
                'student.name as student_name',
                'student.email as student_email',
                's.id as subject_id',
                's.name as subject_name',
                'l.name as level_name',
                'cr.name as class_name',
            ])
            ->get()
            ->unique(
                fn ($row) =>
                    $row->student_id
                    . ':'
                    . $row->subject_id
            )
            ->values();
    }

    /**
     * Liste des professeurs affectés aux parcours de l'étudiant.
     */
    private function professorsForStudent(
        int $studentId
    ): Collection {
        return DB::table(
                'class_user as cu'
            )
            ->join(
                'prof_assignments as pa',
                function ($join) {
                    $join
                        ->on(
                            'pa.subject_id',
                            '=',
                            'cu.subject_id'
                        )
                        ->on(
                            'pa.class_id',
                            '=',
                            'cu.class_id'
                        );
                }
            )
            ->join(
                'users as professor',
                'professor.id',
                '=',
                'pa.prof_id'
            )
            ->join(
                'subjects as s',
                's.id',
                '=',
                'cu.subject_id'
            )
            ->join(
                'class_rooms as cr',
                'cr.id',
                '=',
                'cu.class_id'
            )
            ->join(
                'levels as l',
                'l.id',
                '=',
                'cr.level_id'
            )
            ->where(
                'cu.user_id',
                $studentId
            )
            ->where(
                'professor.role',
                'prof'
            )
            ->whereColumn(
                'pa.level_id',
                'cr.level_id'
            )
            ->select([
                'professor.id as professor_id',
                'professor.name as professor_name',
                'professor.email as professor_email',
                's.id as subject_id',
                's.name as subject_name',
                'l.name as level_name',
                'cr.name as class_name',
            ])
            ->get()
            ->unique(
                fn ($row) =>
                    $row->professor_id
                    . ':'
                    . $row->subject_id
            )
            ->values();
    }
}
