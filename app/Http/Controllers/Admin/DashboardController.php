<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\ClassSlot;
use App\Models\Course;
use App\Models\Live;
use App\Models\Level;
use App\Models\Subject;
use App\Models\TestAppointment;
use App\Models\User;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use App\Services\ClassSlotService;
use App\Services\PedagogicalStructureService;

class DashboardController extends Controller
{

public function index()
    {
        if(auth()->user()->role == 'prof'){
            return redirect()->route('prof.dashboard');
        }

        $classesCount = ClassRoom::count();
        $coursesCount = Course::count();
        $livesCount = Live::count();
        $usersCount = User::where('role', 'student')->count();
        $professorsCount = User::where('role', 'prof')->count();
        $assignedStudentsCount = User::where('role', 'student')->whereNotNull('class_id')->count();
        $assignmentRate = $usersCount > 0 ? round(($assignedStudentsCount / $usersCount) * 100) : 0;
        $pendingAppointments = TestAppointment::pending()->count();

        $testResultsCount = \App\Models\Result::count();

        $students = User::where('role', 'student')
            ->latest()
            ->paginate(10);

        /*
         * Compatibilité avec les anciens comptes : si le pays ou la ville
         * n'ont pas été enregistrés dans users, on récupère les données du
         * rendez-vous le plus récent possédant la même adresse e-mail.
         */
        $studentEmails = $students->getCollection()
            ->pluck('email')
            ->filter()
            ->map(fn ($email) => mb_strtolower(trim($email)))
            ->unique()
            ->values();

        $appointmentsByEmail = TestAppointment::query()
            ->whereIn('email', $studentEmails)
            ->where(function ($query) {
                $query
                    ->whereNotNull('country')
                    ->orWhereNotNull('city');
            })
            ->latest('id')
            ->get()
            ->unique(function ($appointment) {
                return mb_strtolower(trim($appointment->email));
            })
            ->keyBy(function ($appointment) {
                return mb_strtolower(trim($appointment->email));
            });

        $students->setCollection(
            $students->getCollection()->map(function ($student) use ($appointmentsByEmail) {
                $appointment = $appointmentsByEmail->get(
                    mb_strtolower(trim($student->email))
                );

                $student->display_country = $student->country
                    ?: optional($appointment)->country;

                $student->display_city = $student->city
                    ?: optional($appointment)->city;

                return $student;
            })
        );

        $registrationsByMonth = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);

            return [
                'label' => $date->translatedFormat('M Y'),
                'value' => User::where('role', 'student')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        });
        $maxMonthlyRegistrations = max(1, (int) $registrationsByMonth->max('value'));

        $coursesBySubject = Subject::whereIn('name', ['Arabe', 'Coran'])
            ->withCount('courses')
            ->orderBy('name')
            ->get()
            ->map(fn($subject) => [
                'label' => $subject->name,
                'value' => $subject->courses_count,
            ]);
        $maxCoursesBySubject = max(1, (int) $coursesBySubject->max('value'));

        $studentsByCountry = User::where('role', 'student')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->selectRaw('country, COUNT(*) as total')
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $appointmentsByStatus = [
            'pending' => TestAppointment::where('status', TestAppointment::STATUS_PENDING)->count(),
            'confirmed' => TestAppointment::where('status', TestAppointment::STATUS_CONFIRMED)->count(),
            'cancelled' => TestAppointment::where('status', TestAppointment::STATUS_CANCELLED)->count(),
        ];

        $recentActivities = collect()
            ->concat(User::where('role', 'student')->latest()->limit(3)->get()->map(fn($student) => [
                'type' => 'student',
                'title' => 'Nouvel étudiant',
                'description' => $student->name,
                'date' => $student->created_at,
            ]))
            ->concat(Course::latest()->limit(3)->get()->map(fn($course) => [
                'type' => 'course',
                'title' => 'Cours ajouté',
                'description' => $course->title,
                'date' => $course->created_at,
            ]))
            ->concat(Live::latest()->limit(3)->get()->map(fn($live) => [
                'type' => 'live',
                'title' => 'Live programmé',
                'description' => $live->title,
                'date' => $live->created_at,
            ]))
            ->filter(fn($activity) => $activity['date'])
            ->sortByDesc('date')
            ->take(6)
            ->values();

        return view('admin.dashboard', compact(
        'classesCount',
        'coursesCount',
        'livesCount',
        'usersCount',
        'professorsCount',
        'assignedStudentsCount',
        'assignmentRate',
        'pendingAppointments',
        'testResultsCount',
        'students',
        'registrationsByMonth',
        'maxMonthlyRegistrations',
        'coursesBySubject',
        'maxCoursesBySubject',
        'studentsByCountry',
        'appointmentsByStatus',
        'recentActivities'

        ));


    }

    public function absences(Request $request)
    {
        $query = Absence::query()->with([
            'user',
            'subject',
            'level',
            'classRoom',
            'classSlot',
        ]);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($searchQuery) use ($search) {
                $searchQuery
                    ->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhere('date', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', (int) $request->subject_id);
        }

        if ($request->filled('level_id')) {
            $query->where('level_id', (int) $request->level_id);
        }

        if ($request->filled('class_id')) {
            $query->where(
                'class_id',
                (int) $request->class_id
            );
        }

        if ($request->filled('class_slot_id')) {
            $query->where(
                'class_slot_id',
                (int) $request->class_slot_id
            );
        }

        $allowedSorts = ['date', 'created_at', 'present'];
        $sort = in_array($request->get('sort'), $allowedSorts, true)
            ? $request->get('sort')
            : 'date';
        $direction = $request->get('dir') === 'asc' ? 'asc' : 'desc';

        $absences = $query
            ->orderBy($sort, $direction)
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $subjects = $this->absenceHierarchy();
        $absenceHierarchy = $this->absenceHierarchyArray($subjects);

        return view('admin.absences', compact(
            'absences',
            'subjects',
            'absenceHierarchy'
        ));
    }

    public function show(Absence $absence)
    {
        $absence->load([
            'user',
            'subject',
            'level',
            'classRoom',
            'classSlot',
        ]);

        return view('admin.absences.show', compact('absence'));
    }

    public function edit(Absence $absence)
    {
        $absence->load([
            'user',
            'subject',
            'level',
            'classRoom',
            'classSlot',
        ]);

        $editHierarchy = app(
            PedagogicalStructureService::class
        )->hierarchyForAdmin();

        return view(
            'admin.absences.edit',
            [
                'absence' => $absence,
                'editHierarchy' =>
                    $editHierarchy,
                'selectedSubjectId' =>
                    old(
                        'subject_id',
                        $absence->subject_id
                            ?? $absence
                                ->classSlot
                                ?->subject_id
                    ),
                'selectedLevelId' =>
                    old(
                        'level_id',
                        $absence->level_id
                            ?? $absence
                                ->classSlot
                                ?->level_id
                    ),
                'selectedClassId' =>
                    old(
                        'class_id',
                        $absence->class_id
                            ?? $absence
                                ->classSlot
                                ?->class_id
                    ),
                'selectedSlotId' =>
                    old(
                        'class_slot_id',
                        $absence->class_slot_id
                    ),
            ]
        );
    }

    public function update(
        Request $request,
        Absence $absence
    ) {
        $validated = $request->validate([
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
            'date' => [
                'required',
                'date',
            ],
            'present' => [
                'required',
                'boolean',
            ],
        ]);

        $structure = app(
            PedagogicalStructureService::class
        );

        $slot = $structure->slotForPath(
            (int) $validated['class_slot_id'],
            (int) $validated['subject_id'],
            (int) $validated['level_id'],
            (int) $validated['class_id']
        );

        if (
            !$structure->studentAssignedToSlot(
                (int) $absence->user_id,
                $slot
            )
        ) {
            throw ValidationException::withMessages([
                'class_slot_id' =>
                    'Cet étudiant n’est pas assigné au créneau '
                    . $slot->code
                    . '.',
            ]);
        }

        $absence->update([
            'subject_id' =>
                $slot->subject_id,
            'level_id' =>
                $slot->level_id,
            'class_id' =>
                $slot->class_id,
            'class_slot_id' =>
                $slot->id,
            'date' =>
                $validated['date'],
            'present' =>
                (bool) $validated['present'],
        ]);

        return redirect()
            ->route(
                'admin.absences',
                [
                    'subject_id' =>
                        $slot->subject_id,
                    'level_id' =>
                        $slot->level_id,
                    'class_id' =>
                        $slot->class_id,
                    'class_slot_id' =>
                        $slot->id,
                ]
            )
            ->with(
                'success',
                'Absence modifiée.'
            );
    }

    public function destroy(Absence $absence)
    {
        $absence->delete();

        return back()->with('success', 'Absence supprimée.');
    }

    public function create(Request $request)
    {
        $subjects = $this->absenceHierarchy();
        $absenceHierarchy = $this->absenceHierarchyArray($subjects);
        $studentsByPath = $this->absenceStudentsByPath();

        return view('admin.absences.create', compact(
            'subjects',
            'absenceHierarchy',
            'studentsByPath'
        ));
    }

    public function store(Request $request)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'prof'], true), 403);

        $validated = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'level_id' => ['required', 'integer', 'exists:levels,id'],
            'class_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'class_slot_id' => ['required', 'integer', 'exists:class_slots,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'date' => ['required', 'date'],
            'present' => ['required', 'boolean'],
        ]);

        $level = Level::query()
            ->whereKey($validated['level_id'])
            ->where('subject_id', $validated['subject_id'])
            ->first();

        if (!$level) {
            throw ValidationException::withMessages([
                'level_id' => 'Ce niveau n’appartient pas à la matière sélectionnée.',
            ]);
        }

        $classRoom = ClassRoom::query()
            ->whereKey($validated['class_id'])
            ->where('level_id', $validated['level_id'])
            ->first();

        if (!$classRoom) {
            throw ValidationException::withMessages([
                'class_id' => 'Cette classe n’appartient pas au niveau sélectionné.',
            ]);
        }

        $classSlot = app(
            ClassSlotService::class
        )->slotForPath(
            (int) $validated['class_slot_id'],
            (int) $validated['subject_id'],
            (int) $validated['level_id'],
            (int) $validated['class_id']
        );

        if (!$classSlot) {
            throw ValidationException::withMessages([
                'class_slot_id' =>
                    'Ce créneau n’appartient pas au parcours sélectionné.',
            ]);
        }

        $student = User::query()
            ->whereKey($validated['user_id'])
            ->where('role', User::ROLE_STUDENT)
            ->first();

        if (!$student) {
            throw ValidationException::withMessages([
                'user_id' => 'L’étudiant sélectionné est invalide.',
            ]);
        }

        $hasAssignment = DB::table('class_user')
            ->where(
                'user_id',
                $student->id
            )
            ->where(
                'class_id',
                $classRoom->id
            )
            ->where(
                'subject_id',
                $validated['subject_id']
            )
            ->where(
                'class_slot_id',
                $classSlot->id
            )
            ->exists();

        if (!$hasAssignment) {
            throw ValidationException::withMessages([
                'user_id' =>
                    'Cet étudiant n’est pas assigné au créneau '
                    . $classSlot->code
                    . '. Affectez-le d’abord dans '
                    . 'Matière → Niveau → Classe → Créneau.',
            ]);
        }

        Absence::query()->updateOrCreate(
            [
                'user_id' => $student->id,
                'subject_id' => (int) $validated['subject_id'],
                'level_id' => (int) $validated['level_id'],
                'class_id' => (int) $validated['class_id'],
                'class_slot_id' => (int) $classSlot->id,
                'date' => $validated['date'],
            ],
            [
                'present' => (bool) $validated['present'],
            ]
        );

        return redirect()
            ->route('admin.absences', [
                'subject_id' => $validated['subject_id'],
                'level_id' => $validated['level_id'],
                'class_id' => $validated['class_id'],
                'class_slot_id' => $classSlot->id,
            ])
            ->with('success', 'Présence enregistrée avec succès.');
    }

    private function absenceHierarchy()
    {
        $subjects = Subject::query()
            ->whereHas('levels.classes')
            ->with([
                'levels' => function ($levelQuery) {
                    $levelQuery
                        ->orderBy('order')
                        ->orderBy('name')
                        ->with([
                            'classes.subjects',
                        ]);
                },
            ])
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

        return $subjects
            ->map(function (Subject $subject) {
                $levels = $subject->levels;

                $allowedLevelNames =
                    $this->allowedLevelNamesForSubject(
                        $subject
                    );

                if ($allowedLevelNames !== null) {
                    $levels = $levels->filter(
                        fn (Level $level) =>
                            in_array(
                                $this->normalizePathName(
                                    $level->name
                                ),
                                $allowedLevelNames,
                                true
                            )
                    );
                }

                $levels = $levels
                    ->unique(
                        fn (Level $level) =>
                            $this->normalizePathName(
                                $level->name
                            )
                    )
                    ->values()
                    ->map(function (Level $level) use ($subject) {
                        $classes = $level->classes
                            ->filter(
                                fn (ClassRoom $classRoom) =>
                                    $classRoom->subjects->contains(
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
                                    $level
                                ) {
                                    $slots = app(
                                        ClassSlotService::class
                                    )->syncForPath(
                                        $subject,
                                        $level,
                                        $classRoom
                                    );

                                    $classRoom->setRelation(
                                        'classSlots',
                                        $slots
                                    );

                                    return $classRoom;
                                }
                            );

                        if ($classes->isEmpty()) {
                            return null;
                        }

                        $level->setRelation(
                            'classes',
                            $classes
                        );

                        return $level;
                    })
                    ->filter()
                    ->values();

                if ($levels->isEmpty()) {
                    return null;
                }

                $subject->setRelation(
                    'levels',
                    $levels
                );

                return $subject;
            })
            ->filter()
            ->values();
    }

    private function absenceHierarchyArray($subjects): array
    {
        return $subjects->map(function ($subject) {
            return [
                'id' => (int) $subject->id,
                'name' => $subject->name,
                'levels' => $subject->levels->map(function ($level) {
                    return [
                        'id' => (int) $level->id,
                        'name' => $level->name,
                        'classes' => $level->classes->map(function ($classRoom) {
                            return [
                                'id' => (int) $classRoom->id,
                                'name' => $classRoom->name,
                                'slots' => $classRoom
                                    ->classSlots
                                    ->map(
                                        fn (ClassSlot $slot) => [
                                            'id' => (int) $slot->id,
                                            'code' => $slot->code,
                                        ]
                                    )
                                    ->values()
                                    ->all(),
                            ];
                        })->values()->all(),
                    ];
                })->values()->all(),
            ];
        })->values()->all();
    }

    private function allowedLevelNamesForSubject(
        Subject $subject
    ): ?array {
        return match (
            $this->normalizePathName($subject->name)
        ) {
            'arabe' => [
                'lecture & ecriture',
                'communication',
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

    private function absenceStudentsByPath(): array
    {
        $groups = [];

        /*
         * Seules les assignations structurelles exactes sont utilisées.
         *
         * Clé :
         * subject_id:level_id:class_id:class_slot_id
         */
        $rows = DB::table('class_user as cu')
            ->join(
                'users as u',
                'u.id',
                '=',
                'cu.user_id'
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
            ->join(
                'class_slots as cs',
                'cs.id',
                '=',
                'cu.class_slot_id'
            )
            ->where(
                'u.role',
                User::ROLE_STUDENT
            )
            ->whereNotNull(
                'cu.subject_id'
            )
            ->whereNotNull(
                'cu.class_slot_id'
            )
            ->where(
                'cs.is_active',
                true
            )
            ->whereColumn(
                'l.subject_id',
                'cu.subject_id'
            )
            ->whereColumn(
                'cs.subject_id',
                'cu.subject_id'
            )
            ->whereColumn(
                'cs.level_id',
                'l.id'
            )
            ->whereColumn(
                'cs.class_id',
                'cr.id'
            )
            ->select([
                'u.id',
                'u.name',
                'u.email',
                'cu.subject_id',
                'l.id as level_id',
                'cr.id as class_id',
                'cu.class_slot_id',
                'cs.code as slot_code',
            ])
            ->orderBy(
                'u.name'
            )
            ->get();

        foreach ($rows as $row) {
            $key =
                $row->subject_id
                . ':'
                . $row->level_id
                . ':'
                . $row->class_id
                . ':'
                . $row->class_slot_id;

            $groups[$key][(int) $row->id] = [
                'id' => (int) $row->id,
                'name' => $row->name,
                'email' => $row->email,
            ];
        }

        foreach ($groups as $key => $students) {
            $groups[$key] =
                array_values($students);
        }

        return $groups;
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_profile_photo' => ['nullable', 'boolean'],
        ], [
            'profile_photo.image' => 'Le fichier choisi doit être une image.',
            'profile_photo.mimes' => 'La photo doit être au format JPG, JPEG, PNG ou WEBP.',
            'profile_photo.max' => 'La photo ne doit pas dépasser 4 Mo.',
        ]);

        $user = $request->user();

        if ($request->boolean('remove_profile_photo') && ! $request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $user->profile_photo = null;
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $user->profile_photo = $request->file('profile_photo')->store('profiles', 'public');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return back()->with('success', 'Votre profil a été mis à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw validationException($request, [
                'current_password' => 'Mot de passe actuel incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Mot de passe mis à jour !');
    }

}
