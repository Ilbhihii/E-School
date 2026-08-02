<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use App\Models\Test;
use App\Models\Result;
use App\Models\ProfAssignment;
use App\Mail\AccountActivatedMailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    /**
     * Affiche la page d'assignation des professeurs.
     *
     * Hiérarchie :
     * Matière → Niveau → Classe.
     */
    public function profAssignments()
    {
        $professors = User::query()
            ->where('role', 'prof')
            ->orderBy('name')
            ->get();

        $assignmentHierarchy =
            $this->buildAssignmentHierarchy();

        $subjects = collect($assignmentHierarchy)
            ->map(
                fn (array $subject) =>
                    (object) [
                        'id' => $subject['id'],
                        'name' => $subject['name'],
                    ]
            )
            ->values();

        $assignments = ProfAssignment::query()
            ->with([
                'prof',
                'level',
                'classRoom',
                'subject',
            ])
            ->latest()
            ->get();

        return view(
            'admin.prof-assignments',
            compact(
                'professors',
                'subjects',
                'assignmentHierarchy',
                'assignments'
            )
        );
    }

    /**
     * Enregistrer une nouvelle assignation de professeur
     */
    public function storeProfAssignment(
        Request $request
    ) {
        $validated = $request->validate([
            'prof_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
            'level_id' => [
                'required',
                'integer',
                'exists:levels,id',
            ],
            'class_id' => [
                'required',
                'integer',
                'exists:class_rooms,id',
            ],
            'day_of_week' => [
                'required',
                'integer',
                'between:1,7',
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],
        ], [
            'prof_id.required' =>
                'Veuillez sélectionner un professeur.',
            'subject_id.required' =>
                'Veuillez sélectionner une matière.',
            'level_id.required' =>
                'Veuillez sélectionner un niveau.',
            'class_id.required' =>
                'Veuillez sélectionner une classe.',
            'day_of_week.required' =>
                'Veuillez sélectionner le jour du cours.',
            'day_of_week.between' =>
                'Le jour sélectionné est invalide.',
            'start_time.required' =>
                'Veuillez sélectionner l’heure de début.',
            'start_time.date_format' =>
                'L’heure de début est invalide.',
            'end_time.required' =>
                'Veuillez sélectionner l’heure de fin.',
            'end_time.date_format' =>
                'L’heure de fin est invalide.',
            'end_time.after' =>
                'L’heure de fin doit être après '
                . 'l’heure de début.',
        ]);

        $professor = User::query()
            ->whereKey($validated['prof_id'])
            ->where('role', 'prof')
            ->first();

        if (!$professor) {
            return back()
                ->withInput()
                ->withErrors([
                    'prof_id' =>
                        'Le compte sélectionné '
                        . 'n’est pas un professeur.',
                ]);
        }

        $level = Level::query()
            ->whereKey($validated['level_id'])
            ->where(
                'subject_id',
                $validated['subject_id']
            )
            ->first();

        if (!$level) {
            return back()
                ->withInput()
                ->withErrors([
                    'level_id' =>
                        'Ce niveau n’appartient pas '
                        . 'à la matière sélectionnée.',
                ]);
        }

        $classRoom = ClassRoom::query()
            ->whereKey($validated['class_id'])
            ->where('level_id', $level->id)
            ->whereHas(
                'subjects',
                fn ($query) =>
                    $query->where(
                        'subjects.id',
                        $validated['subject_id']
                    )
            )
            ->first();

        if (!$classRoom) {
            return back()
                ->withInput()
                ->withErrors([
                    'class_id' =>
                        'Cette classe n’appartient pas '
                        . 'au niveau et à la matière '
                        . 'sélectionnés.',
                ]);
        }

        $assignment = ProfAssignment::query()
            ->where([
                'prof_id' =>
                    $professor->id,
                'level_id' =>
                    $level->id,
                'class_id' =>
                    $classRoom->id,
                'subject_id' =>
                    $validated['subject_id'],
            ])
            ->first();

        $ignoreAssignmentId =
            $assignment?->id;

        $professorConflict =
            ProfAssignment::query()
                ->where(
                    'prof_id',
                    $professor->id
                )
                ->where(
                    'day_of_week',
                    $validated['day_of_week']
                )
                ->when(
                    $ignoreAssignmentId,
                    fn ($query) =>
                        $query->whereKeyNot(
                            $ignoreAssignmentId
                        )
                )
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->where(
                    'start_time',
                    '<',
                    $validated['end_time']
                )
                ->where(
                    'end_time',
                    '>',
                    $validated['start_time']
                )
                ->exists();

        if ($professorConflict) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' =>
                        'Ce professeur possède déjà '
                        . 'un cours pendant cet horaire.',
                ]);
        }

        $classConflict =
            ProfAssignment::query()
                ->where(
                    'class_id',
                    $classRoom->id
                )
                ->where(
                    'day_of_week',
                    $validated['day_of_week']
                )
                ->when(
                    $ignoreAssignmentId,
                    fn ($query) =>
                        $query->whereKeyNot(
                            $ignoreAssignmentId
                        )
                )
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->where(
                    'start_time',
                    '<',
                    $validated['end_time']
                )
                ->where(
                    'end_time',
                    '>',
                    $validated['start_time']
                )
                ->exists();

        if ($classConflict) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' =>
                        'Cette classe possède déjà '
                        . 'un autre cours pendant '
                        . 'cet horaire.',
                ]);
        }

        $scheduleData = [
            'day_of_week' =>
                $validated['day_of_week'],
            'start_time' =>
                $validated['start_time'],
            'end_time' =>
                $validated['end_time'],
        ];

        if ($assignment) {
            $assignment->update(
                $scheduleData
            );

            return back()->with(
                'success',
                'L’horaire de cette assignation '
                . 'a été mis à jour avec succès.'
            );
        }

        ProfAssignment::create([
            'prof_id' => $professor->id,
            'subject_id' =>
                $validated['subject_id'],
            'level_id' => $level->id,
            'class_id' => $classRoom->id,
            ...$scheduleData,
        ]);

        return back()->with(
            'success',
            'Assignation et horaire du professeur '
            . 'enregistrés avec succès.'
        );
    }

    /**
     * Supprimer une assignation de professeur
     */
    public function destroyProfAssignment($id)
    {
        ProfAssignment::findOrFail($id)->delete();

        return back()->with('success', 'Assignation supprimée avec succès.');
    }

    public function index()
    {
        // 👇 Seulement les students
        $users = User::where('role', 'student')->withCount('results')->get();

        $totalUsers = User::where('role', 'student')->count();

        $recentUsers = User::where('role', 'student')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'recentUsers'
        ));
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'class_id' => 'nullable|exists:class_rooms,id'
        ]);

        $user->update([
            'class_id' => $request->class_id
        ]);

        return back();
    }

    /**
     * Activate a student account (called via admin dashboard).
     * Now also sets test_passed=true for full access.
     */
    public function activate($id)
    {
        $user = User::where('role', 'student')->findOrFail($id);
        $wasAlreadyActive = (bool) $user->is_active;

        if (! $wasAlreadyActive) {
            $user->is_active = true;
            $user->test_passed = true;
            $user->save();
        }

        // Send activation email
        $emailSent = false;
        try {
            Mail::to($user->email)->send(new AccountActivatedMailable($user));
            $emailSent = true;
        } catch (\Throwable $e) {
            \Log::error('Failed to send activation email to ' . $user->email . ': ' . $e->getMessage());
        }

        $message = $wasAlreadyActive
            ? 'Le compte étudiant était déjà actif.'
            : 'Compte étudiant activé. Le paiement reste géré séparément.';

        if ($emailSent) {
            return back()->with('success', $message . ' L’email de confirmation a été envoyé à ' . $user->email . '.');
        }

        return back()->with('error', $message . ' L’email n’a pas pu être envoyé. Vérifiez les identifiants SMTP Gmail du serveur, videz le cache de configuration, puis cliquez à nouveau sur Activer pour réessayer.');
    }

    public function deactivate($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = false;
        $user->save();
        
        return redirect()->back()->with('success', 'Compte désactivé avec succès.');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Impossible de supprimer un compte administrateur.');
        }

        $email = $user->email;
        $user->delete();

        return redirect()->back()->with('success', "Compte de $email annulé et supprimé avec succès.");
    }

    /**
     * List students without class_id
     */
    public function withoutClass()
    {
        $students = User::where('role', 'student')
                       ->whereNull('class_id')
                       ->latest()
                       ->get();
        
        $classRooms = \App\Models\ClassRoom::all();

        $count = $students->count();

        return view('admin.users.without-class', compact('students', 'classRooms', 'count'));
    }

    public function testResults(User $user)
    {
        if ($user->role !== 'student') {
            abort(404);
        }
        $user->load(['results.test.subject']);
        $testsCount = $user->results->count();
        $avgPercentage = $user->results->avg('percentage') ?? 0;
        return view('admin.tests-results', compact('user', 'testsCount', 'avgPercentage'));
    }

    public function showResult($userId, $testId)
    {
        $user = User::findOrFail($userId);
        $test = Test::with('questions.answers')->findOrFail($testId);

        $result = Result::where('user_id', $userId)
            ->where('test_id', $testId)
            ->firstOrFail();

        // Organiser les réponses
        $studentResponses = [];

        if (isset($result->answers) && is_array($result->answers)) {
            foreach ($test->questions as $question) {
                $selectedIds = $result->answers[$question->id] ?? [];
                $studentAnss = [];
                $correctIds = $question->answers->where('is_correct', true)->pluck('id');
                foreach ($selectedIds as $aid) {
                    $answer = $question->answers->find($aid);
                    if ($answer) {
                        $studentAnss[] = [
                            'text' => $answer->answer,
                            'is_correct' => $correctIds->contains($answer->id)
                        ];
                    }
                }
                $studentResponses[$question->id] = $studentAnss;
            }
        }

        // Result object exactly as task
        $finalResult = (object)[
            'score' => $result->score,
            'total_questions' => $result->total_questions,
            'percentage' => $result->percentage,
            'student_responses' => $studentResponses,
            'created_at' => $result->created_at
        ];

        return view('admin.tests-results-show', compact('user', 'test', 'finalResult'))->with('result', $finalResult);
    }

    /**
     * Show student profile/details
     */
    public function show(User $user)
    {
        if ($user->role !== 'student') {
            abort(404, 'Not a student');
        }

        $user->load(['classRoom', 'results.test.subject']);
        $testsCount = $user->results->count();
        $avgScore = $user->results->avg('percentage') ?? 0;

        return view('admin.users.show', compact('user', 'testsCount', 'avgScore'));
    }

    /**
     * Show edit form for class assignment
     */
    public function edit(User $user)
    {
        if ($user->role !== 'student') {
            abort(404, 'Not a student');
        }

        $classRooms = ClassRoom::all();
        return view('admin.users.edit', compact('user', 'classRooms'));
    }

    /**
     * Page d'assignation des étudiants.
     *
     * Hiérarchie :
     * Matière → Niveau → Classe.
     */
    public function assignClass()
    {
        $students = User::query()
            ->where('role', 'student')
            ->orderBy('name')
            ->get();

        $assignmentHierarchy =
            $this->buildAssignmentHierarchy();

        $subjects = collect($assignmentHierarchy)
            ->map(
                fn (array $subject) =>
                    (object) [
                        'id' => $subject['id'],
                        'name' => $subject['name'],
                    ]
            )
            ->values();

        $assignments = DB::table('class_user')
            ->join(
                'users',
                'class_user.user_id',
                '=',
                'users.id'
            )
            ->join(
                'class_rooms',
                'class_user.class_id',
                '=',
                'class_rooms.id'
            )
            ->leftJoin(
                'levels',
                'class_rooms.level_id',
                '=',
                'levels.id'
            )
            ->leftJoin(
                'subjects',
                'class_user.subject_id',
                '=',
                'subjects.id'
            )
            ->select([
                'class_user.id as pivot_id',
                'class_user.user_id',
                'class_user.class_id',
                'class_user.subject_id',
                'class_rooms.level_id',
                'users.name as student_name',
                'class_rooms.name as class_name',
                'levels.name as level_name',
                'subjects.name as subject_name',
            ])
            ->orderByDesc('class_user.id')
            ->get();

        return view(
            'admin.assign-class',
            compact(
                'students',
                'subjects',
                'assignmentHierarchy',
                'assignments'
            )
        );
    }

    /**
     * Store new student assignment following Subject -> Level -> Class.
     */
    public function storeAssignment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'level_id' => 'required|exists:levels,id',
        ]);

        $level = Level::whereKey($request->level_id)
            ->where('subject_id', $request->subject_id)->first();
        if (! $level) {
            return back()->withInput()->withErrors(['level_id' => 'Ce niveau n’appartient pas à la matière sélectionnée.']);
        }
        $class = ClassRoom::whereKey($request->class_id)
            ->where('level_id', $level->id)
            ->first();
        if (! $class) {
            return back()->withInput()->withErrors(['class_id' => 'Cette classe n’appartient pas au niveau et à la matière sélectionnés.']);
        }

        if (!User::whereKey($request->user_id)->where('role', 'student')->exists()) {
            return back()->withInput()->withErrors(['user_id' => 'L’utilisateur sélectionné n’est pas un étudiant.']);
        }

        $classHasSubject = ClassRoom::whereKey($request->class_id)
            ->whereHas('subjects', fn ($query) => $query->where('subjects.id', $request->subject_id))
            ->exists();

        if (!$classHasSubject) {
            return back()->withInput()->withErrors([
                'class_id' => 'La classe sélectionnée n’est pas liée à cette matière.',
            ]);
        }

        $exists = DB::table('class_user')
            ->where('user_id', $request->user_id)
            ->where('subject_id', $request->subject_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()
                ->with('info', 'Cette matière est déjà assignée à cet étudiant.');
        }

        DB::table('class_user')->insert([
            'user_id' => $request->user_id,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 🔥 Synchroniser users.class_id pour que $user->classRoom() fonctionne
        $this->syncStudentClass((int) $request->user_id);

        return redirect()->back()->with('success', 'Matière assignée avec succès !');
    }

    /**
     * Update student-class assignment
     */
    public function updateAssignment(Request $request, $pivotId)
    {
        $assignment = DB::table('class_user')->where('id', $pivotId)->first();
        abort_unless($assignment, 404);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'level_id' => 'required|exists:levels,id',
        ]);

        $level = Level::whereKey($request->level_id)
            ->where('subject_id', $request->subject_id)
            ->first();
        if (! $level) {
            return back()->withInput()->withErrors(['level_id' => 'Ce niveau n’appartient pas à la matière sélectionnée.']);
        }

        if (! ClassRoom::whereKey($request->class_id)->where('level_id', $level->id)->exists()) {
            return back()->withInput()->withErrors(['class_id' => 'Cette classe n’appartient pas au niveau sélectionné.']);
        }

        if (!User::whereKey($request->user_id)->where('role', 'student')->exists()) {
            return back()->withInput()->withErrors(['user_id' => 'L’utilisateur sélectionné n’est pas un étudiant.']);
        }

        $classHasSubject = ClassRoom::whereKey($request->class_id)
            ->whereHas('subjects', fn ($query) => $query->where('subjects.id', $request->subject_id))
            ->exists();

        if (!$classHasSubject) {
            return back()->withInput()->withErrors([
                'class_id' => 'La classe sélectionnée n’est pas liée à cette matière.',
            ]);
        }

        $duplicateExists = DB::table('class_user')
            ->where('user_id', $request->user_id)
            ->where('subject_id', $request->subject_id)
            ->where('id', '!=', $pivotId)
            ->exists();

        if ($duplicateExists) {
            return back()->withInput()->withErrors([
                'subject_id' => 'Cette matière est déjà assignée à cet étudiant.',
            ]);
        }

        DB::table('class_user')
            ->where('id', $pivotId)
            ->update([
                'user_id' => $request->user_id,
                'class_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'updated_at' => now()
            ]);

        // Synchroniser l'ancien étudiant et le nouveau si l'assignation a été transférée.
        $this->syncStudentClass((int) $assignment->user_id);
        if ((int) $assignment->user_id !== (int) $request->user_id) {
            $this->syncStudentClass((int) $request->user_id);
        }

        return redirect()->back()->with('success', 'Assignation modifiée avec succès!');
    }

    /**
     * Delete student-class assignment
     */
    public function destroyAssignment($pivotId)
    {
        $pivot = DB::table('class_user')->where('id', $pivotId)->first();

        abort_unless($pivot, 404);

        DB::table('class_user')
            ->where('id', $pivotId)
            ->delete();

        $this->syncStudentClass((int) $pivot->user_id);

        return redirect()->back()->with('success', 'Assignation supprimée avec succès!');
    }

    /**
     * Construit la structure active utilisée par les formulaires :
     *
     * Matière
     * └── Niveaux où levels.subject_id = subject.id
     *     └── Classes du niveau liées à la matière dans le pivot.
     */
    private function buildAssignmentHierarchy(): array
    {
        $subjects = Subject::query()
            ->orderByRaw(
                "CASE
                    WHEN LOWER(name) = 'arabe' THEN 1
                    WHEN LOWER(name) = 'coran' THEN 2
                    WHEN LOWER(name) = 'soutien lycée' THEN 3
                    ELSE 4
                END"
            )
            ->orderBy('name')
            ->get();

        $levels = Level::query()
            ->with([
                'classes.subjects',
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return $subjects
            ->map(
                function (
                    Subject $subject
                ) use ($levels) {
                    $subjectLevels = $levels
                        ->where(
                            'subject_id',
                            $subject->id
                        )
                        ->map(
                            function (
                                Level $level
                            ) use ($subject) {
                                $classes = $level
                                    ->classes
                                    ->filter(
                                        fn (
                                            ClassRoom $classRoom
                                        ) =>
                                            $classRoom
                                                ->subjects
                                                ->contains(
                                                    'id',
                                                    $subject->id
                                                )
                                    )
                                    ->sortBy('name')
                                    ->unique('id')
                                    ->values()
                                    ->map(
                                        fn (
                                            ClassRoom $classRoom
                                        ) => [
                                            'id' =>
                                                $classRoom->id,
                                            'name' =>
                                                $classRoom->name,
                                        ]
                                    )
                                    ->all();

                                if (empty($classes)) {
                                    return null;
                                }

                                return [
                                    'id' => $level->id,
                                    'name' => $level->name,
                                    'classes' => $classes,
                                ];
                            }
                        )
                        ->filter()
                        ->unique('id')
                        ->values()
                        ->all();

                    if (empty($subjectLevels)) {
                        return null;
                    }

                    return [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'levels' => $subjectLevels,
                    ];
                }
            )
            ->filter()
            ->values()
            ->all();
    }

    private function syncStudentClass(int $userId): void
    {
        $classId = DB::table('class_user')
            ->where('user_id', $userId)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->value('class_id');

        User::whereKey($userId)->where('role', 'student')->update(['class_id' => $classId]);
    }
}
