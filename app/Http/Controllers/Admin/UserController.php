<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\ClassSlot;
use App\Models\Level;
use App\Models\Subject;
use App\Models\Test;
use App\Models\Result;
use App\Models\ProfAssignment;
use App\Models\Schedule;
use App\Mail\AccountActivatedMailable;
use App\Services\ClassSlotService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UserController extends Controller
{

    /**
     * Affiche la page d'assignation des professeurs.
     *
     * Structure :
     * Professeur + Matière → Niveau → Classe → Créneau.
     *
     * Les créneaux proviennent exclusivement de /admin/schedule.
     */
    public function profAssignments()
    {
        $professors = User::query()
            ->where('role', 'prof')
            ->orderBy('name')
            ->get();

        /*
         * Les affectations professeur utilisent exactement la même
         * structure que /admin/assign-class :
         *
         * Matière → Niveau → Classe → Créneau structurel.
         *
         * IMPORTANT :
         * les créneaux viennent de class_slots et ne dépendent PAS
         * de l'existence d'une ligne dans schedules.
         *
         * buildAssignmentHierarchy() filtre déjà uniquement
         * les matières dont subjects.status = active.
         */
        $assignmentHierarchy =
            $this->buildAssignmentHierarchy();

        /*
         * IMPORTANT :
         * le select "Matière" doit afficher TOUTES les matières
         * dont subjects.status = active, même si leur structure
         * Niveau → Classe → Créneau n'est pas encore complète.
         *
         * Avant, la liste était reconstruite depuis
         * $assignmentHierarchy. Donc une matière Active sans
         * structure complète disparaissait du select.
         */
        $subjects = Subject::query()
            ->where(
                'status',
                'active'
            )
            ->get()
            ->sortBy(function (Subject $subject) {
                $normalized =
                    $this->normalizePathName(
                        $subject->name
                    );

                $officialOrder = [
                    'arabe' => 1,
                    'coran' => 2,
                    'soutien lycee' => 3,
                    'soutient lycee' => 3,
                ];

                if (
                    isset(
                        $officialOrder[$normalized]
                    )
                ) {
                    return sprintf(
                        '0-%02d-%s',
                        $officialOrder[$normalized],
                        $normalized
                    );
                }

                return '1-99-' . $normalized;
            })
            ->values();

        /*
         * Les anciennes affectations d'une matière devenue
         * Inactive / Bientôt disponible restent en base,
         * mais ne sont pas affichées sur cette page.
         */
        $assignments = ProfAssignment::query()
            ->with([
                'prof',
                'level',
                'classRoom',
                'subject',
                'classSlot',
            ])
            ->whereHas(
                'subject',
                fn ($query) =>
                    $query->where(
                        'status',
                        'active'
                    )
            )
            ->latest()
            ->get();

        /*
         * L'emploi du temps est seulement informatif.
         *
         * Si une séance D1/D2/I1... existe déjà dans schedules,
         * on affiche son jour et son horaire dans le tableau.
         * Une affectation professeur peut néanmoins exister
         * sans aucune séance planifiée.
         */
        $scheduleMap = Schedule::query()
            ->active()
            ->whereHas(
                'subjectModel',
                fn ($query) =>
                    $query->where(
                        'status',
                        'active'
                    )
            )
            ->whereNotNull('slot_code')
            ->get()
            ->keyBy(
                fn (Schedule $schedule) =>
                    (int) $schedule->subject_id
                    . ':'
                    . (int) $schedule->level_id
                    . ':'
                    . (int) $schedule->class_id
                    . ':'
                    . strtoupper(
                        trim(
                            (string) $schedule->slot_code
                        )
                    )
            );

        return view(
            'admin.prof-assignments',
            compact(
                'professors',
                'subjects',
                'assignmentHierarchy',
                'assignments',
                'scheduleMap'
            )
        );
    }

    /**
     * Affecter un professeur à un créneau officiel de l'emploi du temps.
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
            'class_slot_id' => [
                'required',
                'integer',
                'exists:class_slots,id',
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
            'class_slot_id.required' =>
                'Veuillez sélectionner un créneau.',
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

        /*
         * Même si quelqu'un modifie manuellement la requête,
         * une matière non active ne peut pas être assignée.
         */
        $subject = Subject::query()
            ->whereKey(
                $validated['subject_id']
            )
            ->where(
                'status',
                'active'
            )
            ->first();

        if (!$subject) {
            return back()
                ->withInput()
                ->withErrors([
                    'subject_id' =>
                        'Cette matière n’est pas active.',
                ]);
        }

        $slotService =
            app(ClassSlotService::class);

        /*
         * Vérifie que le créneau appartient réellement au chemin :
         * Matière → Niveau → Classe.
         */
        $slot = $slotService->slotForPath(
            (int) $validated['class_slot_id'],
            (int) $subject->id,
            (int) $validated['level_id'],
            (int) $validated['class_id']
        );

        if (!$slot) {
            return back()
                ->withInput()
                ->withErrors([
                    'class_slot_id' =>
                        'Ce créneau n’appartient pas au parcours '
                        . 'Matière → Niveau → Classe sélectionné.',
                ]);
        }

        /*
         * Un seul professeur principal par créneau structurel.
         * Si D1 est déjà affecté, l'assignation est mise à jour.
         */
        $assignment = ProfAssignment::query()
            ->where(
                'class_slot_id',
                $slot->id
            )
            ->first();

        /*
         * Une séance dans /admin/schedule est optionnelle.
         * On la cherche uniquement pour synchroniser éventuellement
         * le professeur et recopier l'horaire.
         */
        $schedule = Schedule::query()
            ->active()
            ->whereHas(
                'subjectModel',
                fn ($query) =>
                    $query->where(
                        'status',
                        'active'
                    )
            )
            ->where(
                'subject_id',
                $slot->subject_id
            )
            ->where(
                'level_id',
                $slot->level_id
            )
            ->where(
                'class_id',
                $slot->class_id
            )
            ->whereRaw(
                'UPPER(TRIM(slot_code)) = ?',
                [
                    strtoupper(
                        trim(
                            (string) $slot->code
                        )
                    ),
                ]
            )
            ->first();

        $assignmentData = [
            'prof_id' =>
                $professor->id,
            'subject_id' =>
                $slot->subject_id,
            'level_id' =>
                $slot->level_id,
            'class_id' =>
                $slot->class_id,
            'class_slot_id' =>
                $slot->id,
            'day_of_week' =>
                $schedule?->day_of_week,
            'start_time' =>
                $schedule?->start_time,
            'end_time' =>
                $schedule?->end_time,
        ];

        if ($assignment) {
            $assignment->update(
                $assignmentData
            );
        } else {
            ProfAssignment::create(
                $assignmentData
            );
        }

        /*
         * Si la séance existe déjà, elle récupère aussi le professeur.
         * Sans séance, l'affectation reste parfaitement valide.
         */
        if ($schedule) {
            $schedule->update([
                'prof_id' =>
                    $professor->id,
            ]);
        }

        return back()->with(
            'success',
            'Le professeur '
            . $professor->name
            . ' a été affecté au créneau '
            . $slot->code
            . ' avec succès.'
        );
    }

    /**
     * Supprimer l'affectation du professeur au créneau.
     */
    public function destroyProfAssignment($id)
    {
        /*
         * ProfAssignment n'a volontairement PAS de relation "schedule".
         * Le lien principal est classSlot.
         */
        $assignment = ProfAssignment::query()
            ->with('classSlot')
            ->findOrFail($id);

        /*
         * Si une séance correspondant au même créneau structurel
         * existe, on retire aussi le professeur de cette séance.
         */
        if ($assignment->classSlot) {
            $schedule = Schedule::query()
                ->active()
                ->where(
                    'subject_id',
                    $assignment->subject_id
                )
                ->where(
                    'level_id',
                    $assignment->level_id
                )
                ->where(
                    'class_id',
                    $assignment->class_id
                )
                ->whereRaw(
                    'UPPER(TRIM(slot_code)) = ?',
                    [
                        strtoupper(
                            trim(
                                (string) $assignment
                                    ->classSlot
                                    ->code
                            )
                        ),
                    ]
                )
                ->where(
                    'prof_id',
                    $assignment->prof_id
                )
                ->first();

            if ($schedule) {
                $schedule->update([
                    'prof_id' => null,
                ]);
            }
        }

        $assignment->delete();

        return back()->with(
            'success',
            'Assignation du professeur supprimée avec succès.'
        );
    }

    public function index()
    {
        $users = User::query()
            ->where('role', 'student')
            ->withCount('results')
            ->get();

        $totalUsers = User::query()
            ->where('role', 'student')
            ->count();

        $recentUsers = User::query()
            ->where('role', 'student')
            ->latest()
            ->take(5)
            ->get();

        /*
         * Parcours affiché dans /admin/users :
         * Matière → Niveau → Classe → Créneau.
         *
         * class_user définit l'affectation pédagogique et schedules
         * fournit les créneaux officiels de la classe.
         */
        $studentIds = $users->pluck('id');

        $assignmentRows = $studentIds->isEmpty()
            ? collect()
            : DB::table('class_user')
                ->join(
                    'class_rooms',
                    'class_user.class_id',
                    '=',
                    'class_rooms.id'
                )
                ->join(
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
                ->whereIn('class_user.user_id', $studentIds)
                ->whereNotNull('class_user.subject_id')
                ->select([
                    'class_user.user_id',
                    'class_user.subject_id',
                    'class_user.class_id',
                    'class_rooms.level_id',
                    'class_rooms.name as class_name',
                    'levels.name as level_name',
                    'subjects.name as subject_name',
                ])
                ->orderBy('subjects.name')
                ->orderBy('levels.order')
                ->orderBy('class_rooms.name')
                ->get()
                ->unique(
                    fn ($row) =>
                        $row->user_id
                        . ':' . $row->subject_id
                        . ':' . $row->class_id
                )
                ->values();

        $scheduleGroups = Schedule::query()
            ->active()
            ->whereIn(
                'class_id',
                $assignmentRows->pluck('class_id')->unique()
            )
            ->whereIn(
                'subject_id',
                $assignmentRows->pluck('subject_id')->unique()
            )
            ->orderByRaw('COALESCE(day_of_week, 8) asc')
            ->orderByRaw('TIME(start_time) asc')
            ->get()
            ->groupBy(
                fn (Schedule $schedule) =>
                    (int) $schedule->subject_id
                    . ':'
                    . (int) $schedule->class_id
            );

        $studentPaths = $assignmentRows
            ->groupBy('user_id')
            ->map(function ($rows) use ($scheduleGroups) {
                return $rows
                    ->map(function ($row) use ($scheduleGroups) {
                        $key = (int) $row->subject_id
                            . ':'
                            . (int) $row->class_id;

                        $slots = $scheduleGroups
                            ->get($key, collect())
                            ->map(fn (Schedule $schedule) => [
                                'id' => (int) $schedule->id,
                                'label' => $schedule->slot_label,
                            ])
                            ->values()
                            ->all();

                        return [
                            'subject' => $row->subject_name ?: 'Matière',
                            'level' => $row->level_name ?: 'Niveau',
                            'class' => $row->class_name ?: 'Classe',
                            'slots' => $slots,
                        ];
                    })
                    ->values();
            });

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'recentUsers',
            'studentPaths'
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
    public function edit(
        Request $request,
        User $user
    ) {
        if ($user->role !== 'student') {
            abort(404, 'Not a student');
        }

        $assignmentHierarchy =
            $this->buildAssignmentHierarchy();

        $assignments = DB::table('class_user')
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
            ->leftJoin(
                'class_slots',
                'class_user.class_slot_id',
                '=',
                'class_slots.id'
            )
            ->where(
                'class_user.user_id',
                $user->id
            )
            ->select([
                'class_user.id as pivot_id',
                'class_user.user_id',
                'class_user.subject_id',
                'class_rooms.level_id',
                'class_user.class_id',
                'class_user.class_slot_id',
                'subjects.name as subject_name',
                'levels.name as level_name',
                'class_rooms.name as class_name',
                'class_slots.code as slot_code',
            ])
            ->orderBy('subjects.name')
            ->orderBy('levels.name')
            ->orderBy('class_rooms.name')
            ->orderBy('class_slots.position')
            ->get();

        $selectedAssignment = null;

        $requestedPivot =
            (int) $request->query(
                'assignment_id',
                0
            );

        if ($requestedPivot) {
            $selectedAssignment =
                $assignments->firstWhere(
                    'pivot_id',
                    $requestedPivot
                );

            abort_unless(
                $selectedAssignment,
                404
            );
        }

        return view(
            'admin.users.edit',
            compact(
                'user',
                'assignments',
                'selectedAssignment',
                'assignmentHierarchy'
            )
        );
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

        /*
         * Les créneaux viennent de la structure pédagogique
         * Matière → Niveau → Classe → Créneau.
         *
         * Ils ne dépendent plus de /admin/schedule.
         */
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
            ->leftJoin(
                'class_slots',
                'class_user.class_slot_id',
                '=',
                'class_slots.id'
            )
            ->where(
                'subjects.status',
                'active'
            )
            ->select([
                'class_user.id as pivot_id',
                'class_user.user_id',
                'class_user.class_id',
                'class_user.subject_id',
                'class_user.class_slot_id',
                'class_rooms.level_id',
                'users.name as student_name',
                'class_rooms.name as class_name',
                'levels.name as level_name',
                'subjects.name as subject_name',
                'class_slots.code as slot_code',
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
    public function storeAssignment(
        Request $request,
        ClassSlotService $classSlotService
    ) {
        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],
            'level_id' => [
                'required',
                'exists:levels,id',
            ],
            'class_id' => [
                'required',
                'exists:class_rooms,id',
            ],
            'class_slot_id' => [
                'required',
                'exists:class_slots,id',
            ],
        ], [
            'class_slot_id.required' =>
                'Veuillez choisir un créneau.',
        ]);

        $student = User::query()
            ->whereKey($request->user_id)
            ->where('role', 'student')
            ->first();

        if (!$student) {
            return back()
                ->withInput()
                ->withErrors([
                    'user_id' =>
                        'L’utilisateur sélectionné n’est pas un étudiant.',
                ]);
        }

        $subject = Subject::query()
            ->whereKey(
                $request->subject_id
            )
            ->where(
                'status',
                'active'
            )
            ->first();

        if (!$subject) {
            return back()
                ->withInput()
                ->withErrors([
                    'subject_id' =>
                        'Cette matière n’est pas active.',
                ]);
        }

        $level = Level::query()
            ->whereKey($request->level_id)
            ->where(
                'subject_id',
                $subject->id
            )
            ->first();

        if (!$level) {
            return back()
                ->withInput()
                ->withErrors([
                    'level_id' =>
                        'Ce niveau n’appartient pas à la matière sélectionnée.',
                ]);
        }

        $class = ClassRoom::query()
            ->whereKey($request->class_id)
            ->where(
                'level_id',
                $level->id
            )
            ->whereHas(
                'subjects',
                fn ($query) =>
                    $query->where(
                        'subjects.id',
                        $subject->id
                    )
            )
            ->first();

        if (!$class) {
            return back()
                ->withInput()
                ->withErrors([
                    'class_id' =>
                        'Cette classe n’appartient pas au parcours sélectionné.',
                ]);
        }

        $classSlotService->syncForPath(
            $subject,
            $level,
            $class
        );

        $slot =
            $classSlotService->slotForPath(
                (int) $request->class_slot_id,
                (int) $subject->id,
                (int) $level->id,
                (int) $class->id
            );

        if (!$slot) {
            return back()
                ->withInput()
                ->withErrors([
                    'class_slot_id' =>
                        'Ce créneau n’appartient pas à la matière, au niveau et à la classe sélectionnés.',
                ]);
        }

        $exists = DB::table('class_user')
            ->where(
                'user_id',
                $student->id
            )
            ->where(
                'subject_id',
                $subject->id
            )
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with(
                    'info',
                    'Cette matière est déjà assignée à cet étudiant. Utilisez Modifier pour changer sa classe ou son créneau.'
                );
        }

        $values = [
            'user_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'class_slot_id' => $slot->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        /*
         * Compatibilité avec l'ancien système :
         * schedule_id peut encore exister en base, mais
         * l'assignation étudiant ne dépend plus d'une séance.
         */
        if (
            Schema::hasColumn(
                'class_user',
                'schedule_id'
            )
        ) {
            $values['schedule_id'] = null;
        }

        DB::table('class_user')
            ->insert($values);

        $this->syncStudentClass(
            (int) $student->id
        );

        return back()->with(
            'success',
            'Étudiant assigné au créneau '
            . $slot->code
            . ' avec succès.'
        );
    }

    /**
     * Update student-class assignment
     */
    public function updateAssignment(
        Request $request,
        $pivotId,
        ClassSlotService $classSlotService
    ) {
        $assignment = DB::table('class_user')
            ->where('id', $pivotId)
            ->first();

        abort_unless($assignment, 404);

        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],
            'level_id' => [
                'required',
                'exists:levels,id',
            ],
            'class_id' => [
                'required',
                'exists:class_rooms,id',
            ],
            'class_slot_id' => [
                'required',
                'exists:class_slots,id',
            ],
        ]);

        $student = User::query()
            ->whereKey($request->user_id)
            ->where('role', 'student')
            ->first();

        if (!$student) {
            return back()
                ->withInput()
                ->withErrors([
                    'user_id' =>
                        'L’utilisateur sélectionné n’est pas un étudiant.',
                ]);
        }

        $subject = Subject::query()
            ->whereKey(
                $request->subject_id
            )
            ->where(
                'status',
                'active'
            )
            ->first();

        if (!$subject) {
            return back()
                ->withInput()
                ->withErrors([
                    'subject_id' =>
                        'Cette matière n’est pas active.',
                ]);
        }

        $level = Level::query()
            ->whereKey($request->level_id)
            ->where(
                'subject_id',
                $subject->id
            )
            ->first();

        if (!$level) {
            return back()
                ->withInput()
                ->withErrors([
                    'level_id' =>
                        'Ce niveau n’appartient pas à la matière sélectionnée.',
                ]);
        }

        $class = ClassRoom::query()
            ->whereKey($request->class_id)
            ->where(
                'level_id',
                $level->id
            )
            ->whereHas(
                'subjects',
                fn ($query) =>
                    $query->where(
                        'subjects.id',
                        $subject->id
                    )
            )
            ->first();

        if (!$class) {
            return back()
                ->withInput()
                ->withErrors([
                    'class_id' =>
                        'Cette classe n’appartient pas au parcours sélectionné.',
                ]);
        }

        $classSlotService->syncForPath(
            $subject,
            $level,
            $class
        );

        $slot =
            $classSlotService->slotForPath(
                (int) $request->class_slot_id,
                (int) $subject->id,
                (int) $level->id,
                (int) $class->id
            );

        if (!$slot) {
            return back()
                ->withInput()
                ->withErrors([
                    'class_slot_id' =>
                        'Ce créneau n’appartient pas au parcours sélectionné.',
                ]);
        }

        $duplicateExists = DB::table('class_user')
            ->where(
                'user_id',
                $student->id
            )
            ->where(
                'subject_id',
                $subject->id
            )
            ->where(
                'id',
                '!=',
                $pivotId
            )
            ->exists();

        if ($duplicateExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'subject_id' =>
                        'Cette matière est déjà assignée à cet étudiant.',
                ]);
        }

        $values = [
            'user_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'class_slot_id' => $slot->id,
            'updated_at' => now(),
        ];

        if (
            Schema::hasColumn(
                'class_user',
                'schedule_id'
            )
        ) {
            $values['schedule_id'] = null;
        }

        DB::table('class_user')
            ->where('id', $pivotId)
            ->update($values);

        $this->syncStudentClass(
            (int) $assignment->user_id
        );

        if (
            (int) $assignment->user_id
            !== (int) $student->id
        ) {
            $this->syncStudentClass(
                (int) $student->id
            );
        }

        return back()->with(
            'success',
            'Assignation modifiée : créneau '
            . $slot->code
            . '.'
        );
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
     * Hiérarchie de la page /admin/prof-assignments.
     *
     * Source unique des créneaux : schedules.
     * Une classe n'affiche donc que les créneaux réellement créés dans
     * /admin/schedule, avec leur code (D1, I2...), jour et horaire.
     */
    private function buildProfAssignmentHierarchy(): array
    {
        /*
         * Même source que /admin/assign-class.
         *
         * buildAssignmentHierarchy() :
         * - utilise class_slots ;
         * - génère/synchronise les 4 créneaux ;
         * - ne dépend pas de schedules ;
         * - affiche uniquement les matières Active.
         */
        return $this->buildAssignmentHierarchy();
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
        $subjectOrder = [
            'arabe' => 1,
            'coran' => 2,
            'soutien lycee' => 3,
            'soutient lycee' => 3,
        ];

        $subjects = Subject::query()
            ->where(
                'status',
                'active'
            )
            ->get()
            ->sortBy(
                function (
                    Subject $subject
                ) use ($subjectOrder) {
                    $normalized =
                        $this->normalizePathName(
                            $subject->name
                        );

                    if (
                        isset(
                            $subjectOrder[$normalized]
                        )
                    ) {
                        return sprintf(
                            '0-%02d-%s',
                            $subjectOrder[$normalized],
                            $normalized
                        );
                    }

                    return '1-99-' . $normalized;
                }
            )
            ->values();

        $levels = Level::query()
            ->with([
                'classes.subjects',
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $slotService =
            app(ClassSlotService::class);

        return $subjects
            ->map(
                function (
                    Subject $subject
                ) use (
                    $levels,
                    $slotService
                ) {
                    $subjectLevels = $levels
                        ->where(
                            'subject_id',
                            $subject->id
                        );

                    $allowedLevelNames =
                        $this->allowedLevelNamesForSubject(
                            $subject
                        );

                    if ($allowedLevelNames !== null) {
                        $subjectLevels = $subjectLevels
                            ->filter(
                                fn (Level $level) =>
                                    in_array(
                                        $this->normalizePathName(
                                            $level->name
                                        ),
                                        $allowedLevelNames,
                                        true
                                    )
                            )
                            ->sortBy(
                                function (
                                    Level $level
                                ) use (
                                    $allowedLevelNames
                                ) {
                                    $position = array_search(
                                        $this->normalizePathName(
                                            $level->name
                                        ),
                                        $allowedLevelNames,
                                        true
                                    );

                                    return $position === false
                                        ? PHP_INT_MAX
                                        : $position;
                                }
                            );
                    }

                    $subjectLevels = $subjectLevels
                        ->unique(
                            fn (Level $level) =>
                                $this->normalizePathName(
                                    $level->name
                                )
                        )
                        ->values()
                        ->map(
                            function (
                                Level $level
                            ) use (
                                $subject,
                                $slotService
                            ) {
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
                                        function (
                                            ClassRoom $classRoom
                                        ) use (
                                            $subject,
                                            $level,
                                            $slotService
                                        ) {
                                            /*
                                             * Génération automatique des
                                             * 4 créneaux structurels.
                                             * Aucun emploi du temps requis.
                                             */
                                            $slots =
                                                $slotService
                                                    ->syncForPath(
                                                        $subject,
                                                        $level,
                                                        $classRoom
                                                    )
                                                    ->map(
                                                        fn (
                                                            ClassSlot $slot
                                                        ) => [
                                                            'id' =>
                                                                $slot->id,
                                                            'code' =>
                                                                $slot->code,
                                                            'name' =>
                                                                $slot->code,
                                                        ]
                                                    )
                                                    ->values()
                                                    ->all();

                                            return [
                                                'id' =>
                                                    $classRoom->id,
                                                'name' =>
                                                    $classRoom->name,
                                                'slots' =>
                                                    $slots,
                                            ];
                                        }
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
                        ->values()
                        ->all();

                    /*
                     * Ne pas supprimer une matière Active de la
                     * hiérarchie lorsqu'elle n'a pas encore de
                     * niveau/classe exploitable.
                     *
                     * Elle reste visible dans le select Matière.
                     * Si sa structure n'est pas complète, le select
                     * Niveau restera simplement vide.
                     */
                    return [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'levels' => $subjectLevels,
                    ];
                }
            )
            ->values()
            ->all();
    }

    /**
     * Liste officielle des parcours actuellement utilisés.
     * null signifie : aucun filtre spécial pour cette matière.
     */
    private function allowedLevelNamesForSubject(
        Subject $subject
    ): ?array {
        return match (
            $this->normalizePathName($subject->name)
        ) {
            'arabe' => [
                'communication',
                'lecture & ecriture',
            ],
            'coran' => [
                'apprentissage & tajwid',
            ],
            'soutien lycee' => [
                'bac',
            ],
            default => null,
        };
    }

    /**
     * Uniformise accents, majuscules et espaces pour comparer les noms.
     */
    private function normalizePathName(
        string $value
    ): string {
        $value = preg_replace(
            '/\\s+/u',
            ' ',
            trim($value)
        );

        return Str::lower(
            Str::ascii((string) $value)
        );
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
