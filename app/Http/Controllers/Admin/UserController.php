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
use App\Models\ProfessorAvailability;
use App\Models\Schedule;
use App\Mail\AccountActivatedMailable;
use App\Mail\StudentAccountCreatedMailable;
use App\Services\ClassSlotService;
use App\Services\ProfessorAssignmentService;
use App\Services\ProfessorAutoSchedulerService;
use App\Services\PedagogicalTimeSlotService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    /**
     * Affiche la page d'assignation des professeurs.
     *
     * Structure :
     * Professeur + Matière → Niveau → Classe → Jour/Heure → Groupe automatique.
     *
     * Même logique que pour les étudiants : l'horaire détermine le groupe.
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

        $professorTimeSlotMap = app(
            PedagogicalTimeSlotService::class
        )->map();

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
                'schedules',
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
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy(
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
                'professorTimeSlotMap',
                'assignments',
                'scheduleMap'
            )
        );
    }

    /**
     * Disponibilités du ou des professeurs au format attendu
     * par admin.partials.prof-assignment-builder :
     *
     * [
     *   professor_id => [
     *      ['id' => 10, 'label' => 'Lundi · 09:00 – 10:30'],
     *      ...
     *   ]
     * ]
     */
    private function professorAvailabilityMap(
        array $professorIds = []
    ): array {
        $query = ProfessorAvailability::query()
            ->orderBy('prof_id')
            ->orderBy('day_of_week')
            ->orderBy('start_time');

        if (!empty($professorIds)) {
            $query->whereIn(
                'prof_id',
                array_map(
                    'intval',
                    $professorIds
                )
            );
        }

        return $query
            ->get()
            ->groupBy(
                fn (ProfessorAvailability $availability) =>
                    (string) $availability->prof_id
            )
            ->map(
                fn ($items) =>
                    $items
                        ->map(
                            fn (ProfessorAvailability $availability) => [
                                'id' =>
                                    (int) $availability->id,
                                'label' =>
                                    $availability->day_label
                                    . ' · '
                                    . $availability->range_label,
                            ]
                        )
                        ->values()
                        ->all()
            )
            ->all();
    }
    /**
     * Affecter un professeur à un créneau officiel de l'emploi du temps.
     */
    public function storeProfAssignment(
        Request $request
    ) {
        /*
         * Compatibilité avec l'ancien formulaire : si une ancienne
         * soumission envoie encore subject_id / level_id / class_id /
         * class_slot_id à plat, elle est convertie en une ligne.
         */
        if (
            !$request->has('assignments')
            && $request->filled('class_slot_id')
        ) {
            $request->merge([
                'assignments' => [[
                    'subject_id' =>
                        $request->input('subject_id'),
                    'level_id' =>
                        $request->input('level_id'),
                    'class_id' =>
                        $request->input('class_id'),
                    'class_slot_id' =>
                        $request->input('class_slot_id'),
                    'assignment_day_of_week' =>
                        $request->input('assignment_day_of_week'),
                    'assignment_start_time' =>
                        $request->input('assignment_start_time'),
                    'weekly_sessions' =>
                        $request->input('weekly_sessions', 1),
                ]],
            ]);
        }

        $validated = $request->validate([
            'prof_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'assignments' => [
                'required',
                'array',
                'min:1',
                'max:100',
            ],
            'assignments.*.subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
            'assignments.*.level_id' => [
                'required',
                'integer',
                'exists:levels,id',
            ],
            'assignments.*.class_id' => [
                'required',
                'integer',
                'exists:class_rooms,id',
            ],
            /*
             * Le groupe n'est plus choisi par l'administrateur.
             * Il sera calculé depuis le jour + l'heure dans
             * normalizeProfessorAssignmentRows().
             */
            'assignments.*.class_slot_id' => [
                'nullable',
                'integer',
                'exists:class_slots,id',
            ],
            'assignments.*.assignment_day_of_week' => [
                'required',
                'integer',
                'between:1,7',
            ],
            'assignments.*.assignment_start_time' => [
                'required',
                'date_format:H:i',
            ],
            'assignments.*.weekly_sessions' => [
                'required',
                'integer',
                'min:1',
                'max:7',
            ],
        ], [
            'prof_id.required' =>
                'Veuillez sélectionner un professeur.',
            'assignments.required' =>
                'Ajoutez au moins une affectation.',
            'assignments.min' =>
                'Ajoutez au moins une affectation.',
            'assignments.*.subject_id.required' =>
                'Chaque ligne doit avoir une matière.',
            'assignments.*.level_id.required' =>
                'Chaque ligne doit avoir un niveau.',
            'assignments.*.class_id.required' =>
                'Chaque ligne doit avoir une classe.',
            'assignments.*.assignment_day_of_week.required' =>
                'Chaque ligne doit avoir un jour.',
            'assignments.*.assignment_start_time.required' =>
                'Chaque ligne doit avoir une heure. Le groupe sera calculé automatiquement.',
            'assignments.*.weekly_sessions.required' =>
                'Indiquez le nombre de séances par semaine.',
            'assignments.*.weekly_sessions.min' =>
                'Une affectation doit avoir au moins une séance par semaine.',
            'assignments.*.weekly_sessions.max' =>
                'Le maximum autorisé est de 7 séances par semaine.',
        ]);

        $validated['assignments'] =
            $this->normalizeProfessorAssignmentRows(
                $validated['assignments']
            );

        $professor = User::query()
            ->whereKey(
                $validated['prof_id']
            )
            ->where(
                'role',
                User::ROLE_PROF
            )
            ->first();

        if (!$professor) {
            return back()
                ->withInput()
                ->withErrors([
                    'prof_id' =>
                        'Le compte sélectionné n’est pas un professeur.',
                ]);
        }

        $result = app(
            ProfessorAssignmentService::class
        )->add(
            $professor,
            $validated['assignments']
        );

        $this->applyWeeklySessionCounts(
            $professor,
            $validated['assignments']
        );

        /*
         * L'assignation professeur suit désormais la même règle que
         * l'assignation étudiant : le jour et l'heure déterminent le groupe.
         * On ne relance pas le planificateur basé sur ProfessorAvailability,
         * afin de ne pas écraser le créneau choisi par l'administrateur.
         */
        $message = $result['total']
            . ' affectation(s) enregistrée(s) pour '
            . $professor->name
            . '.';

        return back()->with('success', $message);
    }

    /**
     * Modifier toutes les affectations actives d'un professeur.
     */
    public function editProfAssignments(
        User $professor
    ) {
        abort_unless(
            $professor->role === User::ROLE_PROF,
            404
        );

        $assignmentHierarchy =
            $this->buildAssignmentHierarchy();

        $professorTimeSlotMap = app(
            PedagogicalTimeSlotService::class
        )->map();

        $assignments = ProfAssignment::query()
            ->with([
                'subject',
                'level',
                'classRoom',
                'classSlot',
            ])
            ->where(
                'prof_id',
                $professor->id
            )
            ->whereHas(
                'subject',
                fn ($query) =>
                    $query->where(
                        'status',
                        'active'
                    )
            )
            ->whereNotNull(
                'class_slot_id'
            )
            ->orderBy('subject_id')
            ->orderBy('level_id')
            ->orderBy('class_id')
            ->orderBy('class_slot_id')
            ->get();

        $selectedAssignments = $assignments
            ->map(
                fn (ProfAssignment $assignment) => [
                    'subject_id' =>
                        (int) $assignment->subject_id,
                    'level_id' =>
                        (int) $assignment->level_id,
                    'class_id' =>
                        (int) $assignment->class_id,
                    'class_slot_id' =>
                        (int) $assignment->class_slot_id,
                    'assignment_day_of_week' =>
                        $assignment->day_of_week
                            ? (int) $assignment->day_of_week
                            : '',
                    'assignment_start_time' =>
                        $assignment->start_time
                            ? substr(
                                (string) $assignment->start_time,
                                0,
                                5
                            )
                            : '',
                    'weekly_sessions' =>
                        (int) ($assignment->weekly_sessions ?: 1),
                ]
            )
            ->values()
            ->all();

        return view(
            'admin.prof-assignments-edit',
            compact(
                'professor',
                'assignmentHierarchy',
                'professorTimeSlotMap',
                'selectedAssignments'
            )
        );
    }

    /**
     * Remplacer la liste des affectations actives d'un professeur.
     */
    public function updateProfAssignments(
        Request $request,
        User $professor
    ) {
        abort_unless(
            $professor->role === User::ROLE_PROF,
            404
        );

        $validated = $request->validate([
            'assignments' => [
                'required',
                'array',
                'min:1',
                'max:100',
            ],
            'assignments.*.subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
            'assignments.*.level_id' => [
                'required',
                'integer',
                'exists:levels,id',
            ],
            'assignments.*.class_id' => [
                'required',
                'integer',
                'exists:class_rooms,id',
            ],
            'assignments.*.class_slot_id' => [
                'nullable',
                'integer',
                'exists:class_slots,id',
            ],
            'assignments.*.assignment_day_of_week' => [
                'required',
                'integer',
                'between:1,7',
            ],
            'assignments.*.assignment_start_time' => [
                'required',
                'date_format:H:i',
            ],
            'assignments.*.weekly_sessions' => [
                'required',
                'integer',
                'min:1',
                'max:7',
            ],
        ], [
            'assignments.required' =>
                'Ajoutez au moins une affectation.',
            'assignments.min' =>
                'Le professeur doit conserver au moins une affectation. '
                . 'Pour tout supprimer, utilisez les boutons Supprimer '
                . 'depuis la liste principale.',
        ]);

        $validated['assignments'] =
            $this->normalizeProfessorAssignmentRows(
                $validated['assignments']
            );

        $result = app(
            ProfessorAssignmentService::class
        )->replaceActive(
            $professor,
            $validated['assignments']
        );

        $this->applyWeeklySessionCounts(
            $professor,
            $validated['assignments']
        );

        $message = 'Affectations de '
            . $professor->name
            . ' mises à jour : '
            . $result['total']
            . ' active(s), '
            . $result['removed']
            . ' retirée(s).';

        return redirect()
            ->route('admin.users.prof-assignments')
            ->with('success', $message);
    }

    /**
     * Même logique que l'assignation étudiant :
     *
     * Matière -> Niveau -> Classe -> Jour/Heure -> Groupe automatique.
     *
     * Le groupe n'est jamais choisi manuellement. Son numéro correspond
     * au rang chronologique de l'heure dans la journée :
     * 08:00 -> D1, 08:30 -> D2, 08:45 -> D3, 09:00 -> D4, etc.
     * D5, D6... sont créés à la demande sans plafond applicatif.
     */
    private function normalizeProfessorAssignmentRows(
        array $rows
    ): array {
        $timeService = app(
            PedagogicalTimeSlotService::class
        );

        $classSlotService = app(
            ClassSlotService::class
        );

        /*
         * Premier passage : valider les parcours et enregistrer toutes
         * les nouvelles heures. On ne calcule pas encore le groupe car
         * l'ajout d'une heure comme 08:45 peut décaler 09:00 de D3 à D4.
         */
        foreach ($rows as $index => &$row) {
            $day = !empty(
                $row['assignment_day_of_week'] ?? null
            )
                ? (int) $row['assignment_day_of_week']
                : null;

            $rawTime = trim(
                (string) (
                    $row['assignment_start_time']
                    ?? ''
                )
            );

            if (!$day || $rawTime === '') {
                throw ValidationException::withMessages([
                    "assignments.$index.assignment_day_of_week" =>
                        'Choisissez le jour et l’heure. Le groupe sera calculé automatiquement.',
                ]);
            }

            $normalizedTime =
                $timeService->normalizeTime(
                    $rawTime
                );

            if (!$normalizedTime) {
                throw ValidationException::withMessages([
                    "assignments.$index.assignment_start_time" =>
                        'Choisissez une heure comprise entre 08:00 et 22:00.',
                ]);
            }

            $subject = Subject::query()
                ->whereKey(
                    (int) ($row['subject_id'] ?? 0)
                )
                ->where('status', 'active')
                ->first();

            if (!$subject) {
                throw ValidationException::withMessages([
                    "assignments.$index.subject_id" =>
                        'Cette matière n’est pas active.',
                ]);
            }

            $level = Level::query()
                ->whereKey(
                    (int) ($row['level_id'] ?? 0)
                )
                ->where(
                    'subject_id',
                    $subject->id
                )
                ->first();

            if (!$level) {
                throw ValidationException::withMessages([
                    "assignments.$index.level_id" =>
                        'Ce niveau n’appartient pas à la matière sélectionnée.',
                ]);
            }

            $classRoom = ClassRoom::query()
                ->whereKey(
                    (int) ($row['class_id'] ?? 0)
                )
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

            if (!$classRoom) {
                throw ValidationException::withMessages([
                    "assignments.$index.class_id" =>
                        'Cette classe n’appartient pas au parcours sélectionné.',
                ]);
            }

            if (
                !$timeService->slotNumber(
                    $day,
                    $normalizedTime,
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    "assignments.$index.assignment_start_time" =>
                        'Impossible de calculer le groupe pour cet horaire.',
                ]);
            }

            $row['assignment_day_of_week'] =
                $day;
            $row['assignment_start_time'] =
                $normalizedTime;
        }

        unset($row);

        /*
         * Deuxième passage : toutes les heures sont maintenant connues.
         * On recalcule leur rang final puis on crée/récupère D1, D2,
         * D3... I1, I2... A1, A2... sans limite fixe.
         */
        foreach ($rows as $index => &$row) {
            $subject = Subject::query()
                ->whereKey((int) $row['subject_id'])
                ->where('status', 'active')
                ->firstOrFail();

            $level = Level::query()
                ->whereKey((int) $row['level_id'])
                ->where('subject_id', $subject->id)
                ->firstOrFail();

            $classRoom = ClassRoom::query()
                ->whereKey((int) $row['class_id'])
                ->where('level_id', $level->id)
                ->firstOrFail();

            $number =
                $timeService->slotNumber(
                    (int) $row['assignment_day_of_week'],
                    (string) $row['assignment_start_time'],
                    false
                );

            if (!$number) {
                throw ValidationException::withMessages([
                    "assignments.$index.assignment_start_time" =>
                        'Impossible de déterminer le groupe automatique.',
                ]);
            }

            $slot =
                $classSlotService->ensureSlotForNumber(
                    $subject,
                    $level,
                    $classRoom,
                    (int) $number
                );

            $row['class_slot_id'] =
                (int) $slot->id;
        }

        unset($row);

        return $rows;
    }

    /**
     * Enregistre le nombre de séances hebdomadaires demandé pour chaque
     * affectation structurelle (D1/D2/I1/I2/A1...).
     */
    private function applyWeeklySessionCounts(
        User $professor,
        array $rows
    ): void {
        foreach ($rows as $row) {
            if (empty($row['class_slot_id'])) {
                continue;
            }

            ProfAssignment::query()
                ->where('prof_id', $professor->id)
                ->where(
                    'class_slot_id',
                    (int) $row['class_slot_id']
                )
                ->update([
                    'weekly_sessions' => max(
                        1,
                        min(
                            7,
                            (int) ($row['weekly_sessions'] ?? 1)
                        )
                    ),
                ]);
        }
    }

    /**
     * Si les disponibilités ont déjà été reçues, une modification du nombre
     * de séances doit être visible immédiatement dans le planning final.
     */
    private function syncPlanningIfAvailabilityExists(
        User $professor
    ): ?array {
        $hasAvailability = ProfessorAvailability::query()
            ->where('prof_id', $professor->id)
            ->exists();

        if (!$hasAvailability) {
            return null;
        }

        return app(
            ProfessorAutoSchedulerService::class
        )->syncForProfessor($professor);
    }

    /**
     * Supprimer l'affectation du professeur au créneau.
     */
    public function destroyProfAssignment($id)
    {
        $assignment = ProfAssignment::query()
            ->findOrFail($id);

        app(
            ProfessorAssignmentService::class
        )->remove($assignment);

        return back()->with(
            'success',
            'Assignation du professeur supprimée avec succès.'
        );
    }


    /**
     * Affiche le formulaire de création assistée d'un étudiant.
     * L'inscription publique /register reste inchangée.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Crée un étudiant depuis l'administration et lui envoie
     * automatiquement ses accès temporaires par e-mail.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
        ], [
            'name.required' => 'Le nom complet est obligatoire.',
            'email.required' => 'L’adresse e-mail est obligatoire.',
            'email.email' => 'L’adresse e-mail est invalide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'country.required' => 'Le pays est obligatoire.',
            'city.required' => 'La ville est obligatoire.',
        ]);

        $temporaryPassword = $this->generateTemporaryPassword($validated['name']);

        DB::beginTransaction();

        try {
            $student = new User();
            $student->forceFill([
                'name' => trim($validated['name']),
                'email' => mb_strtolower(trim($validated['email'])),
                'phone' => filled($validated['phone'] ?? null) ? trim($validated['phone']) : null,
                'password' => Hash::make($temporaryPassword),
                'role' => User::ROLE_STUDENT,
                'country' => trim($validated['country']),
                'city' => trim($validated['city']),
                'is_active' => true,
                'test_passed' => true,
                'email_verified_at' => now(),
                'must_change_password' => true,
                'temporary_password_expires_at' => now()->addHours(48),
                'temporary_password_sent_at' => now(),
                'password_changed_at' => null,
                'created_by' => auth()->id(),
            ]);
            $student->save();

            Mail::to($student->email)->send(
                new StudentAccountCreatedMailable($student, $temporaryPassword)
            );

            DB::commit();

            return redirect()->route('admin.users.index')->with(
                'success',
                'Compte étudiant créé et accès envoyés à ' . $student->email . '.'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to create assisted student account: ' . $e->getMessage());

            return back()->withInput()->with(
                'error',
                'Le compte n’a pas pu être créé ou l’e-mail n’a pas pu être envoyé.'
            );
        }
    }

    /**
     * Génère un mot de passe temporaire simple à partir du nom complet.
     *
     * Exemple :
     * "ilyas bhihi" => "isb@2026"
     *
     * Règle :
     * - 1re lettre du prénom ;
     * - dernière lettre du prénom ;
     * - 1re lettre du nom de famille ;
     * - @ + année courante.
     */
    private function generateTemporaryPassword(string $fullName): string
    {
        $normalized = Str::ascii($fullName);
        $normalized = strtolower($normalized);

        // Garder uniquement lettres, espaces et tirets
        $normalized = preg_replace(
            '/[^a-z\s\-]/',
            '',
            $normalized
        ) ?? '';

        // Les tirets deviennent des espaces
        $normalized = str_replace(
            '-',
            ' ',
            $normalized
        );

        // Supprimer les espaces multiples
        $normalized = preg_replace(
            '/\s+/',
            ' ',
            trim($normalized)
        ) ?? '';

        $parts = $normalized !== ''
            ? explode(' ', $normalized)
            : [];

        $firstName = $parts[0] ?? 'user';

        $lastName = count($parts) > 1
            ? $parts[count($parts) - 1]
            : $firstName;

        $firstLetter = substr($firstName, 0, 1) ?: 'u';
        $lastLetter = substr($firstName, -1) ?: 'r';
        $familyLetter = substr($lastName, 0, 1) ?: 'u';

        return $firstLetter
            . $lastLetter
            . $familyLetter
            . '@'
            . now()->format('Y');
    }

    public function index()
    {
        $users = User::query()
            ->where('role', 'student')
            ->withCount('results')
            ->with(['studentPayments' => function ($query) {
                $query->orderByDesc('paid_at')->orderByDesc('id');
            }])
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
        if ($user->role !== User::ROLE_STUDENT) {
            abort(404, 'Not a student');
        }

        /*
         * Mise à jour des informations générales du compte.
         * Le paiement reste géré dans le module student-payments afin
         * de conserver tout l'historique 4 mois / annuel.
         */
        if ($request->has('_profile_update')) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users,email,' . $user->id,
                ],
                'phone' => ['nullable', 'string', 'max:30'],
                'country' => ['nullable', 'string', 'max:120'],
                'city' => ['nullable', 'string', 'max:120'],
                'is_active' => ['required', 'boolean'],
            ], [
                'name.required' => 'Le nom complet est obligatoire.',
                'email.required' => 'L’adresse e-mail est obligatoire.',
                'email.email' => 'L’adresse e-mail est invalide.',
                'email.unique' => 'Cette adresse e-mail est déjà utilisée par un autre compte.',
            ]);

            $user->name = trim($validated['name']);
            $user->email = mb_strtolower(trim($validated['email']));
            $user->phone = filled($validated['phone'] ?? null) ? trim($validated['phone']) : null;
            $user->country = filled($validated['country'] ?? null)
                ? trim($validated['country'])
                : null;
            $user->city = filled($validated['city'] ?? null)
                ? trim($validated['city'])
                : null;
            $user->is_active = (bool) $validated['is_active'];
            $user->save();

            return redirect()
                ->route('admin.users.edit', $user)
                ->with('success', 'Les informations de l’étudiant ont été mises à jour avec succès.');
        }

        /*
         * Compatibilité avec l'ancien formulaire de classe.
         */
        $validated = $request->validate([
            'class_id' => 'nullable|exists:class_rooms,id',
        ]);

        $user->class_id = $validated['class_id'] ?? null;
        $user->save();

        return back()->with('success', 'La classe de l’étudiant a été mise à jour.');
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

        $currentPayment = $user->currentStudentPayment()->first();

        $lastPayment = $user->studentPayments()
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->first();

        return view(
            'admin.users.edit',
            compact(
                'user',
                'assignments',
                'selectedAssignment',
                'assignmentHierarchy',
                'currentPayment',
                'lastPayment'
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

        /*
         * Groupe = class_slot_id (A1, A2, D1, I1...).
         * Créneau horaire = schedule_id (jour + heure réelle).
         */
        $studentScheduleMap =
            $this->studentScheduleMap(
                $assignmentHierarchy
            );

        /*
         * Carte réelle des heures déjà connues par jour.
         * Elle sert au navigateur à prévisualiser immédiatement
         * le groupe automatique pour une heure libre (ex. 08:45).
         */
        $studentTimeSlotMap =
            app(
                PedagogicalTimeSlotService::class
            )->map();

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
                'class_user.schedule_id',
                'class_user.student_slot_code',
                'class_user.student_day_of_week',
                'class_user.student_start_time',
                'class_user.student_end_time',
                'class_rooms.level_id',
                'users.name as student_name',
                'class_rooms.name as class_name',
                'levels.name as level_name',
                'subjects.name as subject_name',
                'class_slots.code as slot_code',
            ])
            ->orderByDesc('class_user.id')
            ->get();

        /*
         * STUDENT_ALL_DAYS_TIME_SLOTS_V2
         *
         * Le créneau étudiant est maintenant indépendant de
         * ProfessorAvailability et de schedules.
         *
         * Les anciennes lignes avec schedule_id restent lisibles
         * en secours tant qu'elles n'ont pas été modifiées.
         */
        $studentSchedulesById = Schedule::query()
            ->whereIn(
                'id',
                $assignments
                    ->pluck('schedule_id')
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values()
            )
            ->get()
            ->keyBy('id');

        $assignments->each(function ($assignment) use (
            $studentSchedulesById
        ) {
            $assignment->student_slot_key = null;
            $assignment->schedule_label = null;

            if (
                $assignment->student_day_of_week
                && $assignment->student_start_time
                && $assignment->student_end_time
            ) {
                $dayLabels = [
                    1 => 'Lundi',
                    2 => 'Mardi',
                    3 => 'Mercredi',
                    4 => 'Jeudi',
                    5 => 'Vendredi',
                    6 => 'Samedi',
                    7 => 'Dimanche',
                ];

                $start = substr(
                    (string) $assignment->student_start_time,
                    0,
                    5
                );

                $end = substr(
                    (string) $assignment->student_end_time,
                    0,
                    5
                );

                $minutes =
                    ((int) substr($start, 0, 2) * 60)
                    + (int) substr($start, 3, 2);

                $first =
                    8 * 60;

                $diff =
                    $minutes - $first;

                $keyPart =
                    (
                        $diff >= 0
                        && $diff <= 14 * 60
                        && $diff % 30 === 0
                    )
                        ? (string) (
                            intdiv($diff, 30) + 1
                        )
                        : str_replace(
                            ':',
                            '',
                            $start
                        );

                $assignment->student_slot_key =
                    (int) $assignment->student_day_of_week
                    . ':'
                    . $keyPart;

                /* TIME_SLOT_RANK_V1_LIST_KEY */
                $assignment->student_slot_key =
                    (int)
                        $assignment
                            ->student_day_of_week
                    . '|'
                    . $start;
                $assignment->schedule_label =
                    trim(
                        (string) $assignment->student_slot_code
                    )
                    . ' — '
                    . (
                        $dayLabels[
                            (int) $assignment->student_day_of_week
                        ] ?? 'Jour'
                    )
                    . ' · '
                    . $start;

                return;
            }

            /*
             * Compatibilité avec les anciennes assignations V1.
             */
            $schedule = $assignment->schedule_id
                ? $studentSchedulesById->get(
                    (int) $assignment->schedule_id
                )
                : null;

            if ($schedule) {
                $assignment->schedule_label =
                    $schedule->day_label
                    . ' · '
                    . $schedule->time_range_label;
            }
        });
        return view(
            'admin.assign-class',
            compact(
                'students',
                'subjects',
                'assignmentHierarchy',
                'studentScheduleMap',
                'studentTimeSlotMap',
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
            /*
             * Le groupe n'est plus choisi par l'administrateur.
             * Il est calculé automatiquement à partir du jour
             * et de l'heure.
             */
            'schedule_id' => [
                'required',
                'string',
                'max:32',
            ],
        ], [
            'schedule_id.required' =>
                'Veuillez choisir le jour et l’heure. Le groupe sera calculé automatiquement.',
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
            ->whereKey($request->subject_id)
            ->where('status', 'active')
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
            ->where('subject_id', $subject->id)
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
            ->where('level_id', $level->id)
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

        /*
         * AUTO_GROUP_FROM_TIME_V1
         *
         * Jour + heure -> rang horaire -> groupe.
         * Exemple :
         * Dimanche 08:00 -> D1
         * Dimanche 08:30 -> D2
         * Dimanche 08:45 -> D3
         * Dimanche 09:00 -> D4
         *
         * D5, D6... sont créés automatiquement si nécessaire.
         */
        $studentSchedule =
            $this->resolveStudentSchedule(
                (string) $request->schedule_id,
                $subject,
                $level,
                $class,
                $classSlotService
            );

        if (!$studentSchedule) {
            return back()
                ->withInput()
                ->withErrors([
                    'schedule_id' =>
                        'Créneau étudiant invalide. Choisissez un jour et une heure entre 08:00 et 22:00.',
                ]);
        }

        /** @var \App\Models\ClassSlot $slot */
        $slot =
            $studentSchedule->class_slot;

        $exists = DB::table('class_user')
            ->where('user_id', $student->id)
            ->where('subject_id', $subject->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with(
                    'info',
                    'Cette matière est déjà assignée à cet étudiant. Utilisez Modifier pour changer sa classe ou son créneau horaire.'
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

        if (
            Schema::hasColumn(
                'class_user',
                'schedule_id'
            )
        ) {
            $values['schedule_id'] = null;
        }

        if (
            Schema::hasColumn(
                'class_user',
                'student_slot_code'
            )
        ) {
            $values['student_slot_code'] =
                $studentSchedule->slot_code;

            $values['student_day_of_week'] =
                $studentSchedule->day_of_week;

            $values['student_start_time'] =
                $studentSchedule->start_time;

            $values['student_end_time'] =
                $studentSchedule->end_time;
        }

        DB::transaction(
            function () use (
                $values,
                $slot,
                $subject,
                $class
            ) {
                $this->assertStudentGroupCapacity(
                    $slot,
                    $subject,
                    $class
                );

                DB::table('class_user')
                    ->insert($values);
            }
        );

        $this->syncStudentClass(
            (int) $student->id
        );

        return back()->with(
            'success',
            'Étudiant assigné automatiquement au groupe '
            . $slot->code
            . ' — '
            . $studentSchedule->day_label
            . ' · '
            . $studentSchedule->time_range_label
            . ' — code '
            . $studentSchedule->slot_code
            . '.'
        );
    }

    /**
     * FIX_ASSIGN_CLASS_MODIFIER_DEDICATED_PAGE_V2
     *
     * Page dédiée d'édition d'une assignation étudiant.
     * Cette page ne dépend pas du modal JavaScript de la liste.
     */
    public function editStudentAssignment(
        $pivotId
    ) {
        $assignment = DB::table('class_user')
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
                'class_user.id',
                $pivotId
            )
            ->select([
                'class_user.id as pivot_id',
                'class_user.user_id',
                'class_user.subject_id',
                'class_user.class_id',
                'class_user.class_slot_id',
                'class_user.student_slot_code',
                'class_user.student_day_of_week',
                'class_user.student_start_time',
                'class_user.student_end_time',
                'class_rooms.level_id',
                'users.name as student_name',
                'subjects.name as subject_name',
                'levels.name as level_name',
                'class_rooms.name as class_name',
                'class_slots.code as slot_code',
            ])
            ->first();

        abort_unless(
            $assignment,
            404
        );

        $students = User::query()
            ->where(
                'role',
                'student'
            )
            ->orderBy('name')
            ->get();

        $assignmentHierarchy =
            $this->buildAssignmentHierarchy();

        $studentScheduleMap =
            $this->studentScheduleMap(
                $assignmentHierarchy
            );

        /*
         * Carte réelle des heures déjà connues par jour.
         * Elle sert au navigateur à prévisualiser immédiatement
         * le groupe automatique pour une heure libre (ex. 08:45).
         */
        $studentTimeSlotMap =
            app(
                PedagogicalTimeSlotService::class
            )->map();

        $subjects =
            collect(
                $assignmentHierarchy
            )
                ->map(
                    fn (array $subject) =>
                        (object) [
                            'id' =>
                                $subject['id'],
                            'name' =>
                                $subject['name'],
                        ]
                )
                ->values();

        /*
         * ASSIGNATION_HEURE_LIBRE_MINUTE_V1
         *
         * Clé :
         * - ancienne grille de 30 min => jour:numéro
         * - heure libre hors grille    => jour:HHMM
         */
        $assignment->student_slot_key =
            '';

        if (
            $assignment->student_day_of_week
            && $assignment->student_start_time
        ) {
            $start =
                substr(
                    (string)
                        $assignment
                            ->student_start_time,
                    0,
                    5
                );

            $minutes =
                ((int) substr($start, 0, 2) * 60)
                + (int) substr($start, 3, 2);

            $diff =
                $minutes - (8 * 60);

            $keyPart =
                (
                    $diff >= 0
                    && $diff <= 14 * 60
                    && $diff % 30 === 0
                )
                    ? (string) (
                        intdiv(
                            $diff,
                            30
                        ) + 1
                    )
                    : str_replace(
                        ':',
                        '',
                        $start
                    );

            $assignment
                ->student_slot_key =
                    (int)
                        $assignment
                            ->student_day_of_week
                    . ':'
                    . $keyPart;
        }
        /*
         * SLOT_ORDINAL_EDIT_KEY_V1
         *
         * Le champ caché utilise maintenant jour:HHMM.
         */
        if (
            $assignment->student_day_of_week
            && $assignment->student_start_time
        ) {
            $assignment->student_slot_key =
                (int)
                    $assignment
                        ->student_day_of_week
                . ':'
                . str_replace(
                    ':',
                    '',
                    substr(
                        (string)
                            $assignment
                                ->student_start_time,
                        0,
                        5
                    )
                );
        }
        /* TIME_SLOT_RANK_V1_EDIT_KEY */
        if (
            $assignment->student_day_of_week
            && $assignment->student_start_time
        ) {
            $assignment->student_slot_key =
                (int)
                    $assignment
                        ->student_day_of_week
                . '|'
                . substr(
                    (string)
                        $assignment
                            ->student_start_time,
                    0,
                    5
                );
        }
        return view(
            'admin.assign-class-edit',
            compact(
                'assignment',
                'students',
                'subjects',
                'assignmentHierarchy',
                'studentScheduleMap',
                'studentTimeSlotMap'
            )
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

        abort_unless(
            $assignment,
            404
        );

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
            'schedule_id' => [
                'required',
                'string',
                'max:32',
            ],
        ], [
            'schedule_id.required' =>
                'Veuillez choisir le jour et l’heure. Le groupe sera recalculé automatiquement.',
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
            ->whereKey($request->subject_id)
            ->where('status', 'active')
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
            ->where('subject_id', $subject->id)
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
            ->where('level_id', $level->id)
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

        /*
         * En modification également, l'ancien groupe n'est pas
         * conservé de force : jour + heure recalculent le groupe.
         */
        $studentSchedule =
            $this->resolveStudentSchedule(
                (string) $request->schedule_id,
                $subject,
                $level,
                $class,
                $classSlotService
            );

        if (!$studentSchedule) {
            return back()
                ->withInput()
                ->withErrors([
                    'schedule_id' =>
                        'Créneau étudiant invalide. Choisissez un jour et une heure entre 08:00 et 22:00.',
                ]);
        }

        /** @var \App\Models\ClassSlot $slot */
        $slot =
            $studentSchedule->class_slot;

        $duplicateExists = DB::table('class_user')
            ->where('user_id', $student->id)
            ->where('subject_id', $subject->id)
            ->where('id', '!=', $pivotId)
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

        if (
            Schema::hasColumn(
                'class_user',
                'student_slot_code'
            )
        ) {
            $values['student_slot_code'] =
                $studentSchedule->slot_code;

            $values['student_day_of_week'] =
                $studentSchedule->day_of_week;

            $values['student_start_time'] =
                $studentSchedule->start_time;

            $values['student_end_time'] =
                $studentSchedule->end_time;
        }

        DB::transaction(
            function () use (
                $values,
                $slot,
                $subject,
                $class,
                $pivotId
            ) {
                $this->assertStudentGroupCapacity(
                    $slot,
                    $subject,
                    $class,
                    (int) $pivotId
                );

                DB::table('class_user')
                    ->where('id', $pivotId)
                    ->update($values);
            }
        );

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

        return redirect()
            ->route('admin.assign.class')
            ->with(
                'success',
                'Assignation modifiée : groupe '
                . $slot->code
                . ' — '
                . $studentSchedule->day_label
                . ' · '
                . $studentSchedule->time_range_label
                . ' — code '
                . $studentSchedule->slot_code
                . '.'
            );
    }

    /**
     * STUDENT_GROUP_CAPACITY_V1_SETTINGS
     *
     * Modifier la capacité d'un groupe depuis /admin/assign-class.
     * Valeurs autorisées par le besoin métier : 10 ou 12.
     */
    public function updateStudentGroupCapacity(
        Request $request,
        ClassSlot $slot
    ) {
        abort_unless(
            (bool) $slot->is_active,
            404
        );

        $validated = $request->validate([
            'max_students' => [
                'required',
                'integer',
                'in:10,12',
            ],
        ]);

        $current = (int) DB::table(
            'class_user'
        )
            ->where(
                'subject_id',
                $slot->subject_id
            )
            ->where(
                'class_id',
                $slot->class_id
            )
            ->where(
                'class_slot_id',
                $slot->id
            )
            ->distinct()
            ->count('user_id');

        $newMax =
            (int) $validated[
                'max_students'
            ];

        if ($current > $newMax) {
            throw ValidationException::withMessages([
                'max_students' =>
                    'Impossible de limiter le groupe '
                    . $slot->code
                    . ' à '
                    . $newMax
                    . ' élèves : il contient déjà '
                    . $current
                    . ' élève(s).',
            ]);
        }

        $slot->update([
            'max_students' => $newMax,
        ]);

        return response()->json([
            'ok' => true,
            'slot_id' => (int) $slot->id,
            'code' => (string) $slot->code,
            'current_count' => $current,
            'max_students' => $newMax,
            'available_places' =>
                max(
                    0,
                    $newMax - $current
                ),
            'is_full' =>
                $current >= $newMax,
            'message' =>
                'Capacité du groupe '
                . $slot->code
                . ' enregistrée : '
                . $newMax
                . ' élèves maximum.',
        ]);
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
     * STUDENT_ALL_DAYS_TIME_SLOTS_V2
     *
     * Génère les créneaux étudiants pour chaque matière active.
     *
     * AUCUNE dépendance avec :
     * - les disponibilités des professeurs ;
     * - ProfessorAvailability ;
     * - les lignes schedules.
     *
     * Chaque matière obtient automatiquement 70 possibilités :
     * 7 jours x 10 créneaux.
     */
    /**
     * STUDENT_SLOT_CODE_EXCEL_V4
     *
     * 29 heures de départ fixes de 08:00 à 22:00, par pas de 30 min.
     * Le code final est complété dans resolveStudentSchedule()
     * avec la Classe et le Groupe.
     */
    /**
     * ASSIGNATION_CODES_7J_10_CRENEAUX_V5
     *
     * 7 jours x 10 créneaux.
     * Jour: L, MA, M, J, V, S, D.
     */
    /**
     * ASSIGNATION_JOUR_HEURE_LIBRE_V6
     *
     * Génère pour chaque matière :
     * 7 jours x 29 heures de départ
     * de 08:00 à 22:00, toutes les 30 minutes.
     *
     * Le code complet est finalisé dans
     * resolveStudentSchedule() avec la classe et le groupe.
     */
    private function studentScheduleMap(
        array $assignmentHierarchy
    ): array {
        $dayLabels = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
        ];

        $dayCodes = [
            1 => 'L',
            2 => 'MA',
            3 => 'M',
            4 => 'J',
            5 => 'V',
            6 => 'S',
            7 => 'D',
        ];

        return collect(
            $assignmentHierarchy
        )
            ->mapWithKeys(
                function (
                    array $subject
                ) use (
                    $dayLabels,
                    $dayCodes
                ) {
                    $subjectName =
                        (string) (
                            $subject['name']
                            ?? ''
                        );

                    $normalized =
                        preg_replace(
                            '/[^A-Z0-9]/',
                            '',
                            strtoupper(
                                Str::ascii(
                                    $subjectName
                                )
                            )
                        );

                    $subjectCode =
                        substr(
                            (string)
                                $normalized,
                            0,
                            2
                        );

                    if (
                        $subjectCode === ''
                    ) {
                        $subjectCode =
                            'MT';
                    } elseif (
                        strlen(
                            $subjectCode
                        ) === 1
                    ) {
                        $subjectCode .=
                            'X';
                    }

                    $options = [];

                    foreach (
                        $dayLabels
                        as $day => $dayLabel
                    ) {
                        for (
                            $number = 1;
                            $number <= 29;
                            $number++
                        ) {
                            $start =
                                \Carbon\Carbon
                                    ::createFromFormat(
                                        'H:i',
                                        '08:00'
                                    )
                                    ->addMinutes(
                                        ($number - 1)
                                        * 30
                                    );

                            $end =
                                $start
                                    ->copy()
                                    ->addMinutes(
                                        90
                                    );

                            $baseCode =
                                $dayCodes[$day]
                                . $number
                                . $subjectCode;

                            $options[] = [
                                'id' =>
                                    $day
                                    . ':'
                                    . $number,

                                'code' =>
                                    $baseCode,

                                'base_code' =>
                                    $baseCode,

                                'day_code' =>
                                    $dayCodes[$day],

                                'slot_number' =>
                                    $number,

                                'subject_code' =>
                                    $subjectCode,

                                /*
                                 * L'heure n'est volontairement
                                 * pas dans le label de code.
                                 */
                                'label' =>
                                    $baseCode
                                    . ' — '
                                    . $dayLabel,

                                'day' =>
                                    $dayLabel,

                                'start' =>
                                    $start
                                        ->format(
                                            'H:i'
                                        ),

                                'end' =>
                                    $end
                                        ->format(
                                            'H:i'
                                        ),

                                'time' =>
                                    $start
                                        ->format(
                                            'H:i'
                                        ),
                            ];
                        }
                    }

                    return [
                        (string)
                            $subject['id']
                        =>
                            $options,
                    ];
                }
            )
            ->all();
    }
    /**
     * Convertit la clé synthétique "jour:créneau"
     * en données enregistrables dans class_user.
     *
     * Exemple :
     * matière Arabe + "1:1"
     * => LUAR1 — Lundi · 08:00 – 09:30.
     */
    private function resolveStudentSchedule(
        ?string $scheduleKey,
        Subject $subject,
        Level $level,
        ClassRoom $classRoom,
        ClassSlotService $classSlotService
    ): ?object {
        if (!$scheduleKey) {
            return null;
        }

        $day = null;
        $time = null;

        /*
         * Format courant envoyé par les formulaires :
         * 7|08:45
         */
        if (
            preg_match(
                '/^([1-7])\|(\d{2}:\d{2})$/',
                $scheduleKey,
                $matches
            )
        ) {
            $day = (int) $matches[1];
            $time = $matches[2];
        }

        /*
         * Compatibilité avec les anciennes valeurs "jour:numéro".
         * L'ancien numéro représentait une grille de 30 minutes.
         */
        if (
            !$day
            && preg_match(
                '/^([1-7]):(\d{1,2})$/',
                $scheduleKey,
                $matches
            )
        ) {
            $day = (int) $matches[1];
            $oldNumber = (int) $matches[2];

            if (
                $oldNumber < 1
                || $oldNumber > 99
            ) {
                return null;
            }

            $time =
                \Carbon\Carbon
                    ::createFromFormat(
                        'H:i',
                        '08:00'
                    )
                    ->addMinutes(
                        ($oldNumber - 1)
                        * 30
                    )
                    ->format('H:i');
        }

        if (
            !$day
            || !$time
        ) {
            return null;
        }

        $timeService =
            app(
                PedagogicalTimeSlotService::class
            );

        $normalizedTime =
            $timeService->normalizeTime(
                $time
            );

        if (!$normalizedTime) {
            return null;
        }

        /*
         * Le rang horaire pilote directement le groupe.
         *
         * Avec les heures :
         * 08:00, 08:30, 08:45, 09:00
         * on obtient :
         * D1, D2, D3, D4 pour une classe Débutant.
         *
         * Le rang peut dépasser 4 : D5, D6, D7... sont créés
         * automatiquement.
         */
        $number =
            $timeService->slotNumber(
                $day,
                $normalizedTime,
                true
            );

        if (!$number) {
            return null;
        }

        $slot =
            $classSlotService
                ->ensureSlotForNumber(
                    $subject,
                    $level,
                    $classRoom,
                    (int) $number
                );

        $dayLabels = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
        ];

        $dayCodes = [
            1 => 'L',
            2 => 'MA',
            3 => 'M',
            4 => 'J',
            5 => 'V',
            6 => 'S',
            7 => 'D',
        ];

        $start =
            \Carbon\Carbon
                ::createFromFormat(
                    'H:i',
                    $normalizedTime
                );

        $end =
            $start
                ->copy()
                ->addMinutes(90);

        $codePart =
            static function (
                string $value,
                string $fallback
            ): string {
                $normalized =
                    preg_replace(
                        '/[^A-Z0-9]/',
                        '',
                        strtoupper(
                            Str::ascii(
                                trim($value)
                            )
                        )
                    );

                $code =
                    substr(
                        (string) $normalized,
                        0,
                        2
                    );

                if ($code === '') {
                    return $fallback;
                }

                return strlen($code) === 1
                    ? $code . 'X'
                    : $code;
            };

        $subjectCode =
            $codePart(
                (string) $subject->name,
                'MT'
            );

        $levelCode =
            $codePart(
                (string) $level->name,
                'NV'
            );

        /*
         * Format métier demandé :
         *
         * [Jour]1[Matière][Niveau][Groupe automatique]
         *
         * Arabe -> Lecture & Écriture -> Débutant :
         * Dimanche 08:00 => D1ARLED1
         * Dimanche 08:30 => D1ARLED2
         * Dimanche 08:45 => D1ARLED3
         * Dimanche 09:00 => D1ARLED4
         */
        $slotCode =
            $dayCodes[$day]
            . '1'
            . $subjectCode
            . $levelCode
            . strtoupper(
                trim(
                    (string) $slot->code
                )
            );

        return (object) [
            'id' => null,
            'slot_code' => $slotCode,
            'class_slot' => $slot,
            'class_slot_id' => (int) $slot->id,
            'group_code' => (string) $slot->code,
            'group_number' => (int) $number,
            'day_of_week' => $day,
            'day_label' => $dayLabels[$day],
            'start_time' =>
                $start->format('H:i:s'),
            'end_time' =>
                $end->format('H:i:s'),
            'time_range_label' =>
                $start->format('H:i'),
        ];
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

        /*
         * STUDENT_GROUP_CAPACITY_V1_COUNTS
         *
         * Compteur exact par :
         * Matière + Classe + Groupe.
         *
         * class_slots porte déjà subject_id + level_id + class_id,
         * donc un même code D1 appartenant à un autre parcours
         * reste complètement indépendant.
         */
        $studentCounts = DB::table(
            'class_user'
        )
            ->whereNotNull(
                'subject_id'
            )
            ->whereNotNull(
                'class_id'
            )
            ->whereNotNull(
                'class_slot_id'
            )
            ->select([
                'subject_id',
                'class_id',
                'class_slot_id',
                DB::raw(
                    'COUNT(DISTINCT user_id) as total'
                ),
            ])
            ->groupBy(
                'subject_id',
                'class_id',
                'class_slot_id'
            )
            ->get()
            ->mapWithKeys(
                fn ($row) => [
                    (int) $row->subject_id
                    . ':'
                    . (int) $row->class_id
                    . ':'
                    . (int) $row->class_slot_id
                    => (int) $row->total,
                ]
            );

        return $subjects
            ->map(
                function (
                    Subject $subject
                ) use (
                    $levels,
                    $slotService,
                    $studentCounts
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
                                $slotService,
                                $studentCounts
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
                                            $slotService,
                                            $studentCounts
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
                                                        function (
                                                            ClassSlot $slot
                                                        ) use (
                                                            $subject,
                                                            $classRoom,
                                                            $studentCounts
                                                        ) {
                                                            $key =
                                                                (int) $subject->id
                                                                . ':'
                                                                . (int) $classRoom->id
                                                                . ':'
                                                                . (int) $slot->id;

                                                            $current =
                                                                (int) (
                                                                    $studentCounts[
                                                                        $key
                                                                    ]
                                                                    ?? 0
                                                                );

                                                            $max =
                                                                max(
                                                                    1,
                                                                    (int) (
                                                                        $slot
                                                                            ->max_students
                                                                        ?: 12
                                                                    )
                                                                );

                                                            return [
                                                                'id' =>
                                                                    $slot->id,
                                                                'code' =>
                                                                    $slot->code,
                                                                'name' =>
                                                                    $slot->code,
                                                                'current_count' =>
                                                                    $current,
                                                                'max_students' =>
                                                                    $max,
                                                                'available_places' =>
                                                                    max(
                                                                        0,
                                                                        $max
                                                                        - $current
                                                                    ),
                                                                'is_full' =>
                                                                    $current
                                                                    >= $max,
                                                            ];
                                                        }
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

    /**
     * STUDENT_GROUP_CAPACITY_V1_GUARD
     *
     * Doit être appelé DANS une transaction.
     * Le verrou porte sur la ligne class_slots du groupe.
     */
    private function assertStudentGroupCapacity(
        ClassSlot $slot,
        Subject $subject,
        ClassRoom $classRoom,
        ?int $excludePivotId = null
    ): void {
        /*
         * STUDENT_GROUP_UNLIMITED_CAPACITY_V1
         *
         * Les groupes étudiants n'ont plus de limite de places.
         * La méthode est conservée temporairement pour compatibilité
         * avec les appels existants, mais elle ne bloque plus aucune
         * nouvelle assignation ni modification d'assignation.
         */
        return;
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
