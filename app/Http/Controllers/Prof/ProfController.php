<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Assignment;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Level;
use App\Models\Live;
use App\Models\ProfAssignment;
use App\Models\Subject;
use App\Models\User;
use App\Services\LearningPathService;
use App\Services\ProfessorPathService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfController extends Controller
{
    private LearningPathService $paths;
    private ProfessorPathService $profPaths;

    public function __construct(
        LearningPathService $paths,
        ProfessorPathService $profPaths
    ) {
        $this->paths = $paths;
        $this->profPaths = $profPaths;
    }

    public function dashboard()
    {
        $profAssignments =
            $this->profPaths->assignments(
                auth()->id()
            );

        $studentIds =
            $this->profPaths->studentIds(
                auth()->id()
            );

        $coursesQuery = Course::query()->approved()
            ->where('user_id', auth()->id());

        $devoirsQuery = Assignment::query()
            ->where('user_id', auth()->id());

        $submissionsQuery = Assignment::query()
            ->whereIn('user_id', $studentIds);

        $attendanceQuery = Absence::query()
            ->whereIn('user_id', $studentIds);

        $studentsCount = $studentIds->count();
        $coursesCount = (clone $coursesQuery)->count();
        $myDevoirsCount = (clone $devoirsQuery)->count();
        $assignmentsCount =
            (clone $submissionsQuery)->count();

        $correctedCount =
            (clone $submissionsQuery)
                ->whereNotNull('grade')
                ->count();

        $pendingCount = max(
            $assignmentsCount - $correctedCount,
            0
        );

        $correctionRate =
            $assignmentsCount > 0
                ? round(
                    (
                        $correctedCount
                        / $assignmentsCount
                    ) * 100
                )
                : 0;

        $averageGrade = (float) (
            (clone $submissionsQuery)
                ->whereNotNull('grade')
                ->avg('grade')
            ?? 0
        );

        $absencesCount =
            (clone $attendanceQuery)
                ->where('present', false)
                ->count();

        $attendanceCount =
            (clone $attendanceQuery)->count();

        $presenceRate =
            $attendanceCount > 0
                ? round(
                    (
                        (clone $attendanceQuery)
                            ->where('present', true)
                            ->count()
                        / $attendanceCount
                    ) * 100
                )
                : 100;

        $livesCount = Live::query()
            ->whereIn(
                'class_id',
                $profAssignments
                    ->pluck('class_id')
                    ->unique()
                    ->values()
            )
            ->count();

        $recentSubmissions =
            (clone $submissionsQuery)
                ->with([
                    'user',
                    'subject',
                    'classSlot',
                ])
                ->latest()
                ->take(5)
                ->get();

        return view(
            'prof.dashboard',
            compact(
                'studentsCount',
                'coursesCount',
                'assignmentsCount',
                'myDevoirsCount',
                'correctedCount',
                'pendingCount',
                'correctionRate',
                'averageGrade',
                'absencesCount',
                'presenceRate',
                'livesCount',
                'profAssignments',
                'recentSubmissions'
            )
        );
    }

    /**
     * Copies des étudiants.
     * Structure :
     * Matière → Niveau → Classe → Groupe.
     */
    public function assignments(
        Request $request
    ) {
        $profHierarchy =
            $this->profPaths->hierarchy(
                auth()->id()
            );

        $visibleScope =
            $this->profPaths
                ->filteredAssignments(
                    auth()->id(),
                    $request
                );

        $subjectIds = $visibleScope
            ->pluck('subject_id')
            ->unique()
            ->values();

        $classIds = $visibleScope
            ->pluck('class_id')
            ->unique()
            ->values();

        $studentIds = $visibleScope
            ->flatMap(
                fn (ProfAssignment $assignment) =>
                    $this->profPaths
                        ->studentIdsForAssignment(
                            $assignment
                        )
            )
            ->unique()
            ->values();

        $assignments = Assignment::query()
            ->with([
                'user',
                'subject',
                'classRoom.level',
                'classSlot.subject',
                'classSlot.level',
                'classSlot.classRoom',
            ])
            ->whereIn('user_id', $studentIds)
            ->whereIn('subject_id', $subjectIds)
            ->whereIn('class_room_id', $classIds)
            ->when(
                $request->filled('class_slot_id'),
                fn ($query) =>
                    $query->where(
                        'class_slot_id',
                        (int) $request->query(
                            'class_slot_id'
                        )
                    )
            )
            ->latest()
            ->get();

        $filters =
            $this->profPaths
                ->selectedFilters($request);

        return view(
            'prof.assignments',
            array_merge(
                compact(
                    'assignments',
                    'profHierarchy'
                ),
                $filters
            )
        );
    }

    public function grade(Request $request)
    {
        $request->validate([
            'id' =>
                'required|integer|exists:assignments,id',
            'status' =>
                'required|in:acquis,en_cours,non_acquis',
            'comment' =>
                'nullable|string|max:2000',
        ]);

        $assignment = Assignment::query()
            ->with([
                'user',
                'classSlot',
            ])
            ->whereKey($request->id)
            ->whereHas(
                'user',
                fn ($query) =>
                    $query->where(
                        'role',
                        User::ROLE_STUDENT
                    )
            )
            ->firstOrFail();

        $classRoom = ClassRoom::query()
            ->findOrFail($assignment->class_room_id);

        $scope =
            $assignment->class_slot_id
                ? $this->profPaths->findExactAssignment(
                    auth()->id(),
                    (int) $assignment->subject_id,
                    (int) $classRoom->level_id,
                    (int) $assignment->class_room_id,
                    (int) $assignment->class_slot_id
                )
                : $this->profPaths->findClassAssignment(
                    auth()->id(),
                    (int) $assignment->subject_id,
                    (int) $classRoom->level_id,
                    (int) $assignment->class_room_id
                );

        abort_unless($scope, 403);

        abort_unless(
            $this->profPaths
                ->studentIdsForAssignment($scope)
                ->contains((int) $assignment->user_id),
            403
        );

        $assignment->grade =
            match ($request->status) {
                'acquis' => 20,
                'en_cours' => 10,
                default => 0,
            };

        $assignment->comment =
            $request->comment ?: '';

        $assignment->save();

        $label = match ($assignment->grade) {
            20 => 'Acquis',
            10 => 'En cours d\'acquisition',
            default => 'Non acquis',
        };

        return back()->with(
            'success',
            "Devoir corrigé : {$label}"
        );
    }

    public function absences()
    {
        $profAssignments =
            $this->profPaths->assignments(
                auth()->id()
            );

        $profHierarchy =
            $this->profPaths->hierarchy(
                auth()->id()
            );

        return view(
            'prof.absences',
            compact(
                'profAssignments',
                'profHierarchy'
            )
        );
    }

    public function absencesList(
        Request $request
    ) {
        $profHierarchy =
            $this->profPaths->hierarchy(
                auth()->id()
            );

        $visibleScope =
            $this->profPaths
                ->filteredAssignments(
                    auth()->id(),
                    $request
                );

        $subjectIds = $visibleScope
            ->pluck('subject_id')
            ->unique()
            ->values();

        $classIds = $visibleScope
            ->pluck('class_id')
            ->unique()
            ->values();

        $studentIds = $visibleScope
            ->flatMap(
                fn (ProfAssignment $assignment) =>
                    $this->profPaths
                        ->studentIdsForAssignment(
                            $assignment
                        )
            )
            ->unique()
            ->values();

        $query = Absence::query()
            ->with([
                'user',
                'subject',
                'level',
                'classRoom',
                'classSlot',
            ])
            ->whereIn('user_id', $studentIds)
            ->whereIn('subject_id', $subjectIds)
            ->whereIn('class_id', $classIds);

        $allowedSorts = [
            'date',
            'created_at',
            'present',
        ];

        if (
            $request->filled('sort')
            && in_array(
                $request->sort,
                $allowedSorts,
                true
            )
        ) {
            $query->orderBy(
                $request->sort,
                $request->query('dir') === 'asc'
                    ? 'asc'
                    : 'desc'
            );
        } else {
            $query
                ->orderByDesc('date')
                ->orderByDesc('created_at');
        }

        $absences = $query
            ->paginate(15)
            ->appends(
                $request->query()
            );

        $filters =
            $this->profPaths
                ->selectedFilters($request);

        return view(
            'prof.absences-list',
            array_merge(
                compact(
                    'absences',
                    'profHierarchy'
                ),
                $filters
            )
        );
    }

    public function updateAbsence(
        Request $request,
        $id
    ) {
        $request->validate([
            'present' => 'required|boolean',
        ]);

        $absence = Absence::query()
            ->findOrFail($id);

        $scope =
            $this->profPaths->findClassAssignment(
                auth()->id(),
                (int) $absence->subject_id,
                (int) $absence->level_id,
                (int) $absence->class_id
            );

        abort_unless($scope, 403);

        $absence->present =
            (int) $request->present;

        $absence->save();

        return back()->with(
            'success',
            'Statut de présence mis à jour.'
        );
    }

    /**
     * Étudiants de la classe pour le parcours sélectionné.
     */
    public function getStudents(
        Request $request,
        $id
    ) {
        $validated = $request->validate([
            'subject_id' => ['required', 'integer'],
            'level_id' => ['required', 'integer'],
        ]);

        $scope =
            $this->profPaths
                ->findClassAssignment(
                    auth()->id(),
                    (int) $validated['subject_id'],
                    (int) $validated['level_id'],
                    (int) $id
                );

        abort_unless($scope, 403);

        $studentIds =
            $this->profPaths
                ->studentIdsForAssignment($scope);

        $students = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->whereIn('id', $studentIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($students);
    }

    /**
     * Analyse un rapport de présence Microsoft Teams sans enregistrer
     * immédiatement les absences.
     *
     * Le résultat préremplit l'appel et le professeur garde la validation
     * finale avec le bouton "Enregistrer les présences".
     */
    public function teamsAttendancePreview(
        Request $request
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
            'date' => [
                'required',
                'date',
            ],
            'teams_report' => [
                'required',
                'file',
                'max:10240',
            ],
        ], [
            'teams_report.required' =>
                'Ajoutez le rapport de présence téléchargé depuis Microsoft Teams.',
            'teams_report.file' =>
                'Le rapport Teams envoyé n’est pas un fichier valide.',
            'teams_report.max' =>
                'Le rapport Teams ne doit pas dépasser 10 Mo.',
        ]);

        $extension = Str::lower(
            (string) $request
                ->file('teams_report')
                ->getClientOriginalExtension()
        );

        if (!in_array($extension, ['csv', 'txt'], true)) {
            throw ValidationException::withMessages([
                'teams_report' =>
                    'Utilisez le fichier .CSV téléchargé directement depuis Microsoft Teams.',
            ]);
        }

        $scope =
            $this->profPaths
                ->findClassAssignment(
                    auth()->id(),
                    (int) $validated['subject_id'],
                    (int) $validated['level_id'],
                    (int) $validated['class_id']
                );

        abort_unless($scope, 403);

        $allowedStudentIds =
            $this->profPaths
                ->studentIdsForAssignment($scope)
                ->map(fn ($id) => (int) $id)
                ->values();

        $students = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->whereIn('id', $allowedStudentIds)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'email',
            ]);

        $participants =
            $this->parseTeamsAttendanceReport(
                $request
                    ->file('teams_report')
                    ->getRealPath()
            );

        if (empty($participants)) {
            throw ValidationException::withMessages([
                'teams_report' =>
                    'Aucun participant n’a été trouvé dans ce rapport Teams. '
                    . 'Téléchargez le rapport de présence après la réunion, puis réessayez.',
            ]);
        }

        $matchedParticipantIndexes = [];

        $rows = $students
            ->map(function (User $student) use (
                $participants,
                &$matchedParticipantIndexes
            ) {
                $match = $this->matchTeamsParticipant(
                    $student,
                    $participants,
                    $matchedParticipantIndexes
                );

                if ($match !== null) {
                    $matchedParticipantIndexes[] =
                        (int) $match['index'];
                }

                return [
                    'id' => (int) $student->id,
                    'name' => (string) $student->name,
                    'email' => (string) $student->email,
                    'present' => $match !== null,
                    'teams_matched' => $match !== null,
                    'teams_name' =>
                        $match['participant']['name']
                        ?? null,
                    'teams_email' =>
                        $match['participant']['email']
                        ?? null,
                    'duration' =>
                        $match['participant']['duration']
                        ?? null,
                    'match_method' =>
                        $match['method']
                        ?? null,
                ];
            })
            ->values();

        $matchedIndexes = collect($matchedParticipantIndexes)
            ->unique()
            ->map(fn ($index) => (int) $index)
            ->all();

        $unmatchedTeams = collect($participants)
            ->filter(
                fn ($participant, $index) =>
                    !in_array(
                        (int) $index,
                        $matchedIndexes,
                        true
                    )
            )
            ->map(
                fn ($participant) => [
                    'name' =>
                        $participant['name']
                        ?? '',
                    'email' =>
                        $participant['email']
                        ?? '',
                    'duration' =>
                        $participant['duration']
                        ?? '',
                ]
            )
            ->values();

        return response()->json([
            'students' => $rows,
            'summary' => [
                'students_total' =>
                    $students->count(),
                'present_count' =>
                    $rows
                        ->where('present', true)
                        ->count(),
                'absent_count' =>
                    $rows
                        ->where('present', false)
                        ->count(),
                'teams_participants' =>
                    count($participants),
                'unmatched_teams_count' =>
                    $unmatchedTeams->count(),
            ],
            'unmatched_teams' => $unmatchedTeams,
            'message' =>
                'Rapport Teams analysé. Vérifiez les statuts puis cliquez sur '
                . '« Enregistrer les présences » pour valider définitivement.',
        ]);
    }

    /**
     * Lit les formats CSV/TSV actuellement utilisés par les rapports Teams.
     * Le rapport contient généralement plusieurs sections :
     * 1. Summary
     * 2. Participants
     * 3. In-Meeting Activities
     *
     * Nous utilisons uniquement la section Participants pour éviter
     * les doublons liés aux reconnexions.
     */
    private function parseTeamsAttendanceReport(
        string $path
    ): array {
        $raw = file_get_contents($path);

        if ($raw === false || $raw === '') {
            return [];
        }

        if (str_starts_with($raw, "\xFF\xFE")) {
            $raw = mb_convert_encoding(
                substr($raw, 2),
                'UTF-8',
                'UTF-16LE'
            );
        } elseif (str_starts_with($raw, "\xFE\xFF")) {
            $raw = mb_convert_encoding(
                substr($raw, 2),
                'UTF-8',
                'UTF-16BE'
            );
        } else {
            $raw = preg_replace(
                '/^\xEF\xBB\xBF/',
                '',
                $raw
            ) ?? $raw;
        }

        $lines = preg_split(
            '/\r\n|\n|\r/',
            $raw
        ) ?: [];

        $headerIndex = null;
        $delimiter = ',';
        $headers = [];

        foreach ($lines as $index => $line) {
            if (trim($line) === '') {
                continue;
            }

            $candidateDelimiter =
                $this->detectTeamsCsvDelimiter($line);

            $cells = str_getcsv(
                $line,
                $candidateDelimiter
            );

            $normalized = array_map(
                fn ($value) =>
                    $this->normalizeTeamsHeader(
                        (string) $value
                    ),
                $cells
            );

            $hasName =
                in_array('name', $normalized, true)
                || in_array('full name', $normalized, true)
                || in_array('nom', $normalized, true)
                || in_array('nom complet', $normalized, true);

            $hasParticipantFields =
                in_array('email', $normalized, true)
                || in_array('e mail', $normalized, true)
                || in_array('participant id upn', $normalized, true)
                || in_array('first join', $normalized, true)
                || in_array('premiere connexion', $normalized, true)
                || in_array('in meeting duration', $normalized, true)
                || in_array('duree', $normalized, true);

            if ($hasName && $hasParticipantFields) {
                $headerIndex = (int) $index;
                $delimiter = $candidateDelimiter;
                $headers = $normalized;
                break;
            }
        }

        if ($headerIndex === null) {
            return [];
        }

        $nameIndex =
            $this->teamsHeaderIndex(
                $headers,
                [
                    'name',
                    'full name',
                    'nom',
                    'nom complet',
                ]
            );

        $emailIndex =
            $this->teamsHeaderIndex(
                $headers,
                [
                    'email',
                    'e mail',
                    'adresse e mail',
                    'adresse email',
                ]
            );

        $upnIndex =
            $this->teamsHeaderIndex(
                $headers,
                [
                    'participant id upn',
                    'participant id',
                    'id du participant upn',
                    'identifiant du participant upn',
                    'id participant',
                ]
            );

        $durationIndex =
            $this->teamsHeaderIndex(
                $headers,
                [
                    'in meeting duration',
                    'duration',
                    'duree dans la reunion',
                    'duree de la reunion',
                    'duree',
                ]
            );

        if ($nameIndex === null) {
            return [];
        }

        $participants = [];

        for (
            $index = $headerIndex + 1;
            $index < count($lines);
            $index++
        ) {
            $line = trim(
                (string) $lines[$index]
            );

            if ($line === '') {
                if (!empty($participants)) {
                    break;
                }

                continue;
            }

            $sectionLabel =
                $this->normalizeTeamsHeader(
                    $line
                );

            if (
                !empty($participants)
                && (
                    str_starts_with(
                        $sectionLabel,
                        '3 in meeting'
                    )
                    || str_starts_with(
                        $sectionLabel,
                        '3 activites'
                    )
                    || str_starts_with(
                        $sectionLabel,
                        '3 activities'
                    )
                )
            ) {
                break;
            }

            $cells = str_getcsv(
                $line,
                $delimiter
            );

            $name = trim(
                (string) (
                    $cells[$nameIndex]
                    ?? ''
                )
            );

            if ($name === '') {
                continue;
            }

            $email = $emailIndex !== null
                ? trim(
                    (string) (
                        $cells[$emailIndex]
                        ?? ''
                    )
                )
                : '';

            $upn = $upnIndex !== null
                ? trim(
                    (string) (
                        $cells[$upnIndex]
                        ?? ''
                    )
                )
                : '';

            $duration = $durationIndex !== null
                ? trim(
                    (string) (
                        $cells[$durationIndex]
                        ?? ''
                    )
                )
                : '';

            $email = Str::lower($email);
            $upn = Str::lower($upn);

            $participants[] = [
                'name' => $name,
                'email' =>
                    filter_var(
                        $email,
                        FILTER_VALIDATE_EMAIL
                    )
                        ? $email
                        : '',
                'upn' =>
                    filter_var(
                        $upn,
                        FILTER_VALIDATE_EMAIL
                    )
                        ? $upn
                        : $upn,
                'duration' => $duration,
                'normalized_name' =>
                    $this->normalizeTeamsPersonName(
                        $name
                    ),
                'sorted_name' =>
                    $this->normalizeTeamsPersonName(
                        $name,
                        true
                    ),
            ];
        }

        return $participants;
    }

    private function detectTeamsCsvDelimiter(
        string $line
    ): string {
        $delimiters = [
            "\t",
            ';',
            ',',
        ];

        $best = ',';
        $bestCount = -1;

        foreach ($delimiters as $delimiter) {
            $count = substr_count(
                $line,
                $delimiter
            );

            if ($count > $bestCount) {
                $best = $delimiter;
                $bestCount = $count;
            }
        }

        return $best;
    }

    private function normalizeTeamsHeader(
        string $value
    ): string {
        $value = Str::lower(
            Str::ascii(
                trim($value)
            )
        );

        $value = preg_replace(
            '/[^a-z0-9]+/',
            ' ',
            $value
        ) ?? $value;

        return trim(
            preg_replace(
                '/\s+/',
                ' ',
                $value
            ) ?? $value
        );
    }

    private function teamsHeaderIndex(
        array $headers,
        array $candidates
    ): ?int {
        foreach ($candidates as $candidate) {
            $index = array_search(
                $candidate,
                $headers,
                true
            );

            if ($index !== false) {
                return (int) $index;
            }
        }

        return null;
    }

    private function normalizeTeamsPersonName(
        string $value,
        bool $sortWords = false
    ): string {
        $value = preg_replace(
            '/\([^)]*\)/u',
            ' ',
            $value
        ) ?? $value;

        $value = Str::lower(
            Str::ascii($value)
        );

        $value = preg_replace(
            '/[^a-z0-9]+/',
            ' ',
            $value
        ) ?? $value;

        $words = array_values(
            array_filter(
                preg_split(
                    '/\s+/',
                    trim($value)
                ) ?: []
            )
        );

        if ($sortWords) {
            sort(
                $words,
                SORT_STRING
            );
        }

        return implode(
            ' ',
            $words
        );
    }

    private function matchTeamsParticipant(
        User $student,
        array $participants,
        array $alreadyMatched
    ): ?array {
        $studentEmail = Str::lower(
            trim(
                (string) $student->email
            )
        );

        if ($studentEmail !== '') {
            foreach ($participants as $index => $participant) {
                if (
                    in_array(
                        (int) $index,
                        $alreadyMatched,
                        true
                    )
                ) {
                    continue;
                }

                if (
                    $studentEmail
                    === (
                        $participant['email']
                        ?? ''
                    )
                    || $studentEmail
                    === (
                        $participant['upn']
                        ?? ''
                    )
                ) {
                    return [
                        'index' => (int) $index,
                        'method' => 'email',
                        'participant' =>
                            $participant,
                    ];
                }
            }
        }

        $studentName =
            $this->normalizeTeamsPersonName(
                (string) $student->name
            );

        $studentSortedName =
            $this->normalizeTeamsPersonName(
                (string) $student->name,
                true
            );

        $candidateIndexes = [];

        foreach ($participants as $index => $participant) {
            if (
                in_array(
                    (int) $index,
                    $alreadyMatched,
                    true
                )
            ) {
                continue;
            }

            $sameNormal =
                $studentName !== ''
                && $studentName
                    === (
                        $participant['normalized_name']
                        ?? ''
                    );

            $sameSorted =
                $studentSortedName !== ''
                && $studentSortedName
                    === (
                        $participant['sorted_name']
                        ?? ''
                    );

            if ($sameNormal || $sameSorted) {
                $candidateIndexes[] =
                    (int) $index;
            }
        }

        /*
         * Un nom n'est utilisé automatiquement que s'il donne
         * une seule correspondance. Cela évite les faux positifs
         * quand deux étudiants portent le même nom.
         */
        if (count($candidateIndexes) === 1) {
            $index = $candidateIndexes[0];

            return [
                'index' => $index,
                'method' => 'name',
                'participant' =>
                    $participants[$index],
            ];
        }

        return null;
    }
    public function storeAbsence(
        Request $request
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
            'date' => [
                'required',
                'date',
            ],
            'students' => [
                'required',
                'array',
            ],
            'students.*' => [
                'required',
                'boolean',
            ],
        ]);

        $scope =
            $this->profPaths
                ->findClassAssignment(
                    auth()->id(),
                    (int) $validated['subject_id'],
                    (int) $validated['level_id'],
                    (int) $validated['class_id']
                );

        abort_unless($scope, 403);

        $allowedStudentIds =
            $this->profPaths
                ->studentIdsForAssignment(
                    $scope
                );

        $alertStudents = [];

        foreach (
            $validated['students']
            as $studentId => $status
        ) {
            abort_unless(
                $allowedStudentIds->contains(
                    (int) $studentId
                ),
                403
            );

            Absence::query()->updateOrCreate(
                [
                    'user_id' =>
                        (int) $studentId,
                    'subject_id' =>
                        (int) $validated[
                            'subject_id'
                        ],
                    'level_id' =>
                        (int) $validated[
                            'level_id'
                        ],
                    'class_id' =>
                        (int) $validated[
                            'class_id'
                        ],
                    'class_slot_id' => null,
                    'date' =>
                        $validated['date'],
                ],
                [
                    'present' =>
                        (bool) $status,
                ]
            );

            $absenceCount = Absence::query()
                ->where(
                    'user_id',
                    $studentId
                )
                ->where(
                    'subject_id',
                    (int) $validated['subject_id']
                )
                ->where(
                    'class_id',
                    (int) $validated['class_id']
                )
                ->where(
                    'present',
                    false
                )
                ->whereDate(
                    'date',
                    '<=',
                    $validated['date']
                )
                ->count();

            if ($absenceCount >= 3) {
                $alertStudents[] =
                    (int) $studentId;
            }
        }

        if (!empty($alertStudents)) {
            session()->flash(
                'alert',
                'Certains étudiants ont atteint '
                . 'ou dépassé 3 absences '
                . 'dans cette classe.'
            );
        }

        return back()->with(
            'success',
            'Présences enregistrées avec succès.'
        );
    }

    public function updateProfile(
        Request $request
    ) {
        $user = auth()->user();

        $request->validate([
            'name' =>
                'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')
                    ->ignore($user->id),
            ],
        ]);

        $user->update(
            $request->only(
                'name',
                'email'
            )
        );

        return back()->with(
            'success',
            'Profil mis à jour avec succès !'
        );
    }

    public function updatePassword(
        Request $request
    ) {
        $request->validate([
            'current_password' =>
                'required|current_password',
            'password' =>
                'required|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' =>
                Hash::make(
                    $request->password
                ),
        ]);

        return back()->with(
            'success',
            'Mot de passe mis à jour avec succès !'
        );
    }

    public function browseLives(
        Level $level,
        ClassRoom $class
    ) {
        $scope = ProfAssignment::query()
            ->where('prof_id', auth()->id())
            ->where('level_id', $level->id)
            ->where('class_id', $class->id)
            ->get();

        abort_if($scope->isEmpty(), 403);

        $slotIds = $scope
            ->pluck('class_slot_id')
            ->filter()
            ->values();

        $lives = Live::query()
            ->whereIn(
                'class_slot_id',
                $slotIds
            )
            ->latest()
            ->get();

        return view(
            'prof.lives.browse',
            compact(
                'level',
                'class',
                'lives'
            )
        );
    }

    public function browseDevoirs(
        Level $level,
        ClassRoom $class,
        Subject $subject
    ) {
        $scope = ProfAssignment::query()
            ->with('classSlot')
            ->where('prof_id', auth()->id())
            ->where('subject_id', $subject->id)
            ->where('level_id', $level->id)
            ->where('class_id', $class->id)
            ->get();

        abort_if($scope->isEmpty(), 403);

        $slotCodes = $scope
            ->pluck('classSlot.code')
            ->filter()
            ->values();

        $courses = Course::query()->approved()
            ->where(
                'subject_id',
                $subject->id
            )
            ->where(
                'level_id',
                $level->id
            )
            ->where(
                'class_id',
                $class->id
            )
            ->whereIn(
                'slot_code',
                $slotCodes
            )
            ->where(
                'user_id',
                auth()->id()
            )
            ->with('devoirs')
            ->get();

        return view(
            'prof.devoir.browse',
            compact(
                'level',
                'class',
                'subject',
                'courses'
            )
        );
    }

    /**
     * /prof/lives
     * Matière → Niveau → Classe → Groupe.
     */
    public function livesIndex(
        Request $request
    ) {
        $profHierarchy =
            $this->profPaths->hierarchy(
                auth()->id()
            );

        $visibleScope =
            $this->profPaths
                ->filteredAssignments(
                    auth()->id(),
                    $request
                );

        $classIds = $visibleScope
            ->pluck('class_id')
            ->unique()
            ->values();

        $query = Live::query()
            ->with([
                'classRoom.level',
                'classSlot.subject',
                'classSlot.level',
                'classSlot.classRoom',
            ])
            ->when(
                $classIds->isNotEmpty(),
                fn ($query) =>
                    $query->whereIn('class_id', $classIds),
                fn ($query) =>
                    $query->whereRaw('1 = 0')
            )
            ->when(
                $request->filled('class_slot_id'),
                fn ($query) =>
                    $query->where(
                        'class_slot_id',
                        (int) $request->query(
                            'class_slot_id'
                        )
                    )
            );

        $totalLives =
            (clone $query)->count();

        $upcomingLives =
            (clone $query)
                ->where(function ($query) {
                    $query
                        ->whereDate(
                            'live_date',
                            '>=',
                            now()->toDateString()
                        )
                        ->orWhereNull(
                            'live_date'
                        );
                })
                ->count();

        $recentLives =
            (clone $query)
                ->latest()
                ->limit(5)
                ->get();

        $lives = $query
            ->orderByDesc('live_date')
            ->orderByDesc('start_time')
            ->paginate(15)
            ->appends(
                $request->query()
            );

        $filters =
            $this->profPaths
                ->selectedFilters($request);

        return view(
            'prof.lives.index',
            array_merge(
                compact(
                    'lives',
                    'totalLives',
                    'recentLives',
                    'upcomingLives',
                    'profHierarchy'
                ),
                $filters
            )
        );
    }

    // ═══ Matières → Niveaux → Classes → Groupes ═══

    public function subjectsList()
    {
        $scope =
            $this->profPaths->assignments(
                auth()->id()
            );

        $subjects = Subject::query()
            ->whereIn(
                'id',
                $scope->pluck(
                    'subject_id'
                )
            )
            ->orderBy('name')
            ->get();

        $subjects->each(
            function (
                Subject $subject
            ) use ($scope) {
                $subjectScope =
                    $scope->where(
                        'subject_id',
                        $subject->id
                    );

                $subject->assigned_levels_count =
                    $subjectScope
                        ->pluck('level_id')
                        ->unique()
                        ->count();

                $subject->assigned_classes_count =
                    $subjectScope
                        ->pluck('class_id')
                        ->unique()
                        ->count();

                $subject->assigned_slots_count =
                    $subjectScope
                        ->pluck('class_slot_id')
                        ->filter()
                        ->unique()
                        ->count();
            }
        );

        return view(
            'prof.subjects.index',
            compact('subjects')
        );
    }

    public function subjectLevels(
        Subject $subject
    ) {
        $scope =
            $this->profPaths->assignments(
                auth()->id()
            )
            ->where(
                'subject_id',
                $subject->id
            );

        abort_if(
            $scope->isEmpty(),
            403
        );

        $levels = Level::query()
            ->whereIn(
                'id',
                $scope->pluck(
                    'level_id'
                )
            )
            ->orderBy('name')
            ->get();

        $levels->each(
            function (
                Level $level
            ) use ($scope) {
                $levelScope =
                    $scope->where(
                        'level_id',
                        $level->id
                    );

                $level->assigned_classes_count =
                    $levelScope
                        ->pluck('class_id')
                        ->unique()
                        ->count();

                $level->assigned_slots_count =
                    $levelScope
                        ->pluck('class_slot_id')
                        ->filter()
                        ->unique()
                        ->count();
            }
        );

        return view(
            'prof.subjects.levels',
            compact(
                'subject',
                'levels'
            )
        );
    }

    public function subjectClasses(
        Subject $subject,
        Level $level
    ) {
        abort_unless(
            (int) $level->subject_id
                === (int) $subject->id,
            404
        );

        $scope =
            $this->profPaths->assignments(
                auth()->id()
            )
            ->where(
                'subject_id',
                $subject->id
            )
            ->where(
                'level_id',
                $level->id
            );

        abort_if(
            $scope->isEmpty(),
            403
        );

        $classes = ClassRoom::query()
            ->whereIn(
                'id',
                $scope->pluck(
                    'class_id'
                )
            )
            ->orderBy('name')
            ->get();

        $classes->each(
            function (
                ClassRoom $class
            ) use ($scope) {
                $class->assignedSlots =
                    $scope
                        ->where(
                            'class_id',
                            $class->id
                        )
                        ->pluck(
                            'classSlot'
                        )
                        ->filter()
                        ->sortBy(
                            'position'
                        )
                        ->unique(
                            'id'
                        )
                        ->values();
            }
        );

        return view(
            'prof.subjects.classes',
            compact(
                'subject',
                'level',
                'classes'
            )
        );
    }

    public function subjectCourses(
        Request $request,
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        $scope =
            $this->scopeForClass(
                $subject,
                $level,
                $class
            );

        $selectedSlot =
            $this->selectedSlotFromScope(
                $request,
                $scope
            );

        $slotCodes = $selectedSlot
            ? collect([
                $selectedSlot->code,
            ])
            : $scope
                ->pluck('classSlot.code')
                ->filter()
                ->unique()
                ->values();

        $courses = Course::query()->approved()
            ->where(
                'subject_id',
                $subject->id
            )
            ->where(
                'level_id',
                $level->id
            )
            ->where(
                'class_id',
                $class->id
            )
            ->whereIn(
                'slot_code',
                $slotCodes
            )
            ->where(
                'user_id',
                auth()->id()
            )
            ->with([
                'classRoom',
                'subject',
                'level',
            ])
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        return view(
            'prof.subjects.courses',
            compact(
                'subject',
                'level',
                'class',
                'courses',
                'selectedSlot',
                'scope'
            )
        );
    }

    public function subjectLives(
        Request $request,
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        $scope =
            $this->scopeForClass(
                $subject,
                $level,
                $class
            );

        $selectedSlot =
            $this->selectedSlotFromScope(
                $request,
                $scope
            );

        $slotIds = $selectedSlot
            ? collect([
                $selectedSlot->id,
            ])
            : $scope
                ->pluck('class_slot_id')
                ->filter()
                ->unique()
                ->values();

        $lives = Live::query()
            ->with('classSlot')
            ->whereIn(
                'class_slot_id',
                $slotIds
            )
            ->latest()
            ->get();

        return view(
            'prof.subjects.lives',
            compact(
                'subject',
                'level',
                'class',
                'lives',
                'selectedSlot',
                'scope'
            )
        );
    }

    public function subjectDevoirs(
        Request $request,
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        $scope =
            $this->scopeForClass(
                $subject,
                $level,
                $class
            );

        $selectedSlot =
            $this->selectedSlotFromScope(
                $request,
                $scope
            );

        $slotIds = $selectedSlot
            ? collect([
                $selectedSlot->id,
            ])
            : $scope
                ->pluck('class_slot_id')
                ->filter()
                ->unique()
                ->values();

        $devoirs = Assignment::query()
            ->with([
                'subject',
                'classSlot',
                'course',
            ])
            ->where(
                'user_id',
                auth()->id()
            )
            ->whereIn(
                'class_slot_id',
                $slotIds
            )
            ->latest()
            ->get();

        return view(
            'prof.subjects.devoirs',
            compact(
                'subject',
                'level',
                'class',
                'devoirs',
                'selectedSlot',
                'scope'
            )
        );
    }

    private function scopeForClass(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        abort_unless(
            (int) $level->subject_id
                === (int) $subject->id
            && (int) $class->level_id
                === (int) $level->id,
            404
        );

        $scope =
            $this->profPaths
                ->assignments(
                    auth()->id()
                )
                ->where(
                    'subject_id',
                    $subject->id
                )
                ->where(
                    'level_id',
                    $level->id
                )
                ->where(
                    'class_id',
                    $class->id
                )
                ->values();

        abort_if(
            $scope->isEmpty(),
            403
        );

        return $scope;
    }

    private function selectedSlotFromScope(
        Request $request,
        $scope
    ) {
        $slotId =
            (int) $request->query(
                'class_slot_id',
                0
            );

        if (!$slotId) {
            return null;
        }

        $assignment =
            $scope->first(
                fn (
                    ProfAssignment $assignment
                ) =>
                    (int) $assignment
                        ->class_slot_id
                    === $slotId
            );

        abort_unless(
            $assignment
            && $assignment->classSlot,
            403
        );

        return $assignment->classSlot;
    }

    private function assignedClasses()
    {
        $ids =
            $this->profPaths
                ->assignments(
                    auth()->id()
                )
                ->pluck('class_id')
                ->unique();

        return ClassRoom::query()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get();
    }

    private function assignedStudentIds()
    {
        return $this->profPaths
            ->studentIds(
                auth()->id()
            );
    }

    private function authorizeTeachingScope(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ): void {
        abort_if(
            $this->scopeForClass(
                $subject,
                $level,
                $class
            )->isEmpty(),
            403
        );
    }
}
