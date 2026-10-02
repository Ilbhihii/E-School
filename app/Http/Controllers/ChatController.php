<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Message;
use App\Models\User;
use App\Models\ProfAssignment;
use App\Services\LearningPathService;
use App\Services\ProfessorPathService;
use App\Services\AssignmentScopeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /* =========================
        STUDENT
    ========================= */

    // Liste des matières (filtrée par classe pour les étudiants)
    public function subjects()
    {
        $user = auth()->user();

        abort_unless(
            $user->isStudent(),
            403
        );

        $rows =
            app(
                LearningPathService::class
            )
                ->studentAssignmentRows(
                    $user->id
                )
                ->filter(
                    fn ($row) =>
                        !empty(
                            $row
                                ->student_slot_code
                        )
                )
                ->values();

        $subjectsById =
            Subject::query()
                ->whereIn(
                    'id',
                    $rows
                        ->pluck('subject_id')
                        ->filter()
                        ->unique()
                )
                ->where(
                    'status',
                    'active'
                )
                ->get()
                ->keyBy('id');

        $subjects =
            $rows
                ->map(
                    function ($row) use (
                        $subjectsById
                    ) {
                        $subject =
                            $subjectsById->get(
                                (int) $row
                                    ->subject_id
                            );

                        if (!$subject) {
                            return null;
                        }

                        $space =
                            clone $subject;

                        $space->setAttribute(
                            'assignment_code',
                            strtoupper(
                                trim(
                                    (string)
                                        $row
                                            ->student_slot_code
                                )
                            )
                        );

                        $space->setAttribute(
                            'class_slot_id',
                            (int) (
                                $row
                                    ->class_slot_id
                                ?? 0
                            )
                        );

                        return $space;
                    }
                )
                ->filter()
                ->unique(
                    fn ($subject) =>
                        $subject->id
                        . '|'
                        . $subject
                            ->assignment_code
                )
                ->values();

        $administration =
            Subject::query()
                ->where(
                    'name',
                    'Administration'
                )
                ->first();

        if ($administration) {
            $administration
                ->setAttribute(
                    'assignment_code',
                    null
                );

            $subjects =
                $subjects
                    ->push(
                        $administration
                    )
                    ->values();
        }

        return view(
            'student.chats',
            compact('subjects')
        );
    }

    // Chat pour une matière (used by route:chat) — accès vérifié pour les étudiants
    public function index($subject_id)
    {
        $subject =
            Subject::findOrFail(
                $subject_id
            );

        $user =
            auth()->user();

        abort_unless(
            $user->isStudent(),
            403
        );

        $isAdministration =
            $this
                ->isAdministrationSubject(
                    $subject
                );

        $assignmentCode = null;
        $scopeRow = null;

        if (!$isAdministration) {
            $assignmentCode =
                strtoupper(
                    trim(
                        (string)
                            request(
                                'assignment_code',
                                ''
                            )
                    )
                );

            abort_if(
                $assignmentCode === '',
                403,
                'Choisissez votre groupe pédagogique.'
            );

            $scopeRow =
                app(
                    LearningPathService::class
                )
                    ->studentAssignmentRows(
                        $user->id
                    )
                    ->first(
                        fn ($row) =>
                            (int) $row->subject_id
                                === (int) $subject->id
                            && strtoupper(
                                trim(
                                    (string) (
                                        $row
                                            ->student_slot_code
                                        ?? ''
                                    )
                                )
                            ) === $assignmentCode
                    );

            abort_unless(
                $scopeRow
                && $subject->status === 'active',
                403,
                'Ce groupe ne fait pas partie de votre affectation.'
            );
        }

        $messages =
            Message::query()
                ->where(
                    'subject_id',
                    $subject->id
                )
                ->when(
                    $isAdministration,
                    fn ($query) =>
                        $query->where(
                            'conversation_user_id',
                            $user->id
                        )
                )
                ->when(
                    !$isAdministration,
                    fn ($query) =>
                        $query->where(
                            'assignment_code',
                            $assignmentCode
                        )
                )
                ->with('user')
                ->latest()
                ->get();

        $groupChatContext =
            $isAdministration
                ? $this
                    ->emptyGroupChatContext()
                : $this
                    ->groupChatContext(
                        $subject,
                        $messages,
                        $assignmentCode
                    );

        return view(
            'student.chat',
            compact(
                'subject',
                'messages',
                'isAdministration',
                'groupChatContext',
                'assignmentCode'
            )
        );
    }

    // Envoyer message étudiant
    public function send(Request $request)
    {
        $validated =
            $request->validate([
                'subject_id' => [
                    'required',
                    'integer',
                    'exists:subjects,id',
                ],
                'assignment_code' => [
                    'nullable',
                    'string',
                    'max:64',
                ],
                'message' => [
                    'required',
                    'string',
                    'max:5000',
                ],
            ]);

        $user =
            auth()->user();

        abort_unless(
            $user->isStudent(),
            403
        );

        $subject =
            Subject::findOrFail(
                $validated['subject_id']
            );

        $isAdministration =
            $this
                ->isAdministrationSubject(
                    $subject
                );

        $assignmentCode = null;
        $classSlotId = null;

        if (!$isAdministration) {
            $assignmentCode =
                strtoupper(
                    trim(
                        (string) (
                            $validated[
                                'assignment_code'
                            ]
                            ?? ''
                        )
                    )
                );

            $scope =
                app(
                    LearningPathService::class
                )
                    ->studentAssignmentRows(
                        $user->id
                    )
                    ->first(
                        fn ($row) =>
                            (int) $row->subject_id
                                === (int) $subject->id
                            && strtoupper(
                                trim(
                                    (string) (
                                        $row
                                            ->student_slot_code
                                        ?? ''
                                    )
                                )
                            ) === $assignmentCode
                    );

            abort_unless(
                $scope
                && $subject->status === 'active',
                403,
                'Ce groupe ne fait pas partie de votre affectation.'
            );

            $classSlotId =
                (int) (
                    $scope
                        ->class_slot_id
                    ?? 0
                );
        }

        Message::create([
            'user_id' =>
                $user->id,
            'subject_id' =>
                $subject->id,
            'conversation_user_id' =>
                $isAdministration
                    ? $user->id
                    : null,
            'assignment_code' =>
                $assignmentCode,
            'class_slot_id' =>
                $classSlotId ?: null,
            'message' =>
                $validated['message'],
        ]);

        return back();
    }

    /**
     * Retourne toutes les matières réellement assignées à l'étudiant.
     *
     * L'ancienne version récupérait uniquement la dernière matière
     * enregistrée dans class_user, ce qui masquait les autres chats.
     */
    private function assignedStudentSubjectIds(
        int $userId
    ): Collection {
        return app(LearningPathService::class)
            ->studentAssignmentRows($userId)
            ->pluck('subject_id')
            ->filter()
            ->map(
                fn ($subjectId) => (int) $subjectId
            )
            ->unique()
            ->values();
    }

    private function isAdministrationSubject(Subject $subject): bool
    {
        return mb_strtolower($subject->name) === 'administration';
    }

    // Supprimer message étudiant
    public function delete(Request $request)
    {
        $request->validate([
            'messages' => 'required|array|min:1',
            'messages.*' => 'exists:messages,id'
        ]);

        $deleted = Message::whereIn('id', $request->messages)
            ->where('user_id', auth()->id())
            ->delete();

        if ($deleted > 0) {
            return back()->with('success', "$deleted message(s) supprimé(s) avec succès.");
        }

        return back()->with('error', 'Aucun message valide à supprimer.');
    }


    /* =========================
        ADMIN
    ========================= */

    // Liste des espaces de discussion pour admin
    public function adminIndex()
    {
        /*
         * Les discussions privées utilisent toujours la matière
         * technique « Administration », mais elles sont présentées
         * dans deux espaces distincts : Étudiants et Professeurs.
         */
        $chatSpaces = collect();

        /*
         * Une discussion pédagogique est disponible pour toute
         * matière dont le statut est Active.
         *
         * La matière technique « Administration » est exclue de
         * cette liste car elle conserve ses deux espaces privés :
         * Étudiants et Professeurs.
         */
        $groupSubjects = Subject::query()
            ->where(
                'status',
                'active'
            )
            ->whereRaw(
                'LOWER(TRIM(name)) <> ?',
                ['administration']
            )
            ->get()
            ->unique('id')
            ->sortBy(function (Subject $subject) {
                $normalized =
                    mb_strtolower(
                        trim($subject->name)
                    );

                $officialOrder = [
                    'arabe' => 1,
                    'coran' => 2,
                    'soutien lycée' => 3,
                    'soutient lycée' => 3,
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

        foreach ($groupSubjects as $subject) {
            $messageQuery = Message::query()
                ->where('subject_id', $subject->id);

            $lastMessage = (clone $messageQuery)
                ->latest('created_at')
                ->first();

            $subject->setAttribute(
                'messages_count',
                (clone $messageQuery)->count()
            );
            $subject->setAttribute(
                'conversation_role',
                null
            );
            $subject->setAttribute(
                'space_name',
                $subject->name
            );
            $subject->setRelation(
                'messages',
                collect($lastMessage ? [$lastMessage] : [])
            );

            $chatSpaces->push($subject);
        }

        $administration = Subject::query()
            ->where('name', 'Administration')
            ->first();

        if ($administration) {
            foreach (
                [
                    'student' => 'Étudiants',
                    'prof' => 'Professeurs',
                ] as $role => $label
            ) {
                $messageQuery = Message::query()
                    ->where(
                        'subject_id',
                        $administration->id
                    )
                    ->whereHas(
                        'conversationUser',
                        fn ($query) =>
                            $query->where('role', $role)
                    );

                $lastMessage = (clone $messageQuery)
                    ->latest('created_at')
                    ->first();

                $space = clone $administration;
                $space->setAttribute(
                    'messages_count',
                    (clone $messageQuery)->count()
                );
                $space->setAttribute(
                    'conversation_role',
                    $role
                );
                $space->setAttribute(
                    'space_name',
                    $label
                );
                $space->setRelation(
                    'messages',
                    collect($lastMessage ? [$lastMessage] : [])
                );

                $chatSpaces->push($space);
            }
        }

        $subjects = $chatSpaces->values();

        return view(
            'admin.chat-list',
            compact('subjects')
        );
    }

    // Chat admin pour une matière ou un espace privé
    public function adminChat($subject)
    {
        $subject = Subject::findOrFail($subject);

        abort_unless(
            $this->isAdministrationSubject(
                $subject
            )
            || $subject->status === 'active',
            404,
            'Cette matière n’est pas active pour les discussions.'
        );

        $isAdministration =
            $this->isAdministrationSubject($subject);

        $conversationUsers = collect();
        $selectedConversationUser = null;
        $conversationRole = null;
        $conversationSpaceLabel = $subject->name;

        if ($isAdministration) {
            $selectedConversationUserId = (int) request(
                'contact',
                request('student', 0)
            );

            $requestedRole = (string) request('role', '');

            /*
             * Compatibilité avec les anciens liens qui ne contenaient
             * pas encore le paramètre role.
             */
            if (
                !in_array(
                    $requestedRole,
                    ['student', 'prof'],
                    true
                )
                && $selectedConversationUserId > 0
            ) {
                $requestedRole = (string) User::query()
                    ->whereKey($selectedConversationUserId)
                    ->whereIn('role', ['student', 'prof'])
                    ->value('role');
            }

            if (
                !in_array(
                    $requestedRole,
                    ['student', 'prof'],
                    true
                )
            ) {
                $requestedRole = 'student';
            }

            $conversationRole = $requestedRole;
            $conversationSpaceLabel =
                $conversationRole === 'prof'
                    ? 'Professeurs'
                    : 'Étudiants';

            $conversationUsers = User::query()
                ->where('role', $conversationRole)
                ->orderBy('name')
                ->get()
                ->map(function (User $user) use ($subject) {
                    $conversationQuery = Message::withTrashed()
                        ->where(
                            'subject_id',
                            $subject->id
                        )
                        ->where(
                            'conversation_user_id',
                            $user->id
                        );

                    $user->setAttribute(
                        'conversation_message_count',
                        (clone $conversationQuery)->count()
                    );

                    $user->setAttribute(
                        'conversation_last_message',
                        (clone $conversationQuery)
                            ->latest('created_at')
                            ->first()
                    );

                    return $user;
                })
                ->sortByDesc(
                    fn (User $user) =>
                        optional(
                            $user->conversation_last_message
                        )->created_at?->timestamp ?? 0
                )
                ->values();

            if ($selectedConversationUserId > 0) {
                $selectedConversationUser =
                    $conversationUsers->firstWhere(
                        'id',
                        $selectedConversationUserId
                    );
            }

            if (!$selectedConversationUser) {
                $selectedConversationUser =
                    $conversationUsers->first(
                        fn (User $user) =>
                            $user->conversation_message_count > 0
                    )
                    ?? $conversationUsers->first();
            }
        }

        $messages = Message::with([
                'user',
                'conversationUser',
            ])
            ->where(
                'subject_id',
                $subject->id
            )
            ->when(
                $isAdministration,
                function ($query) use (
                    $selectedConversationUser
                ) {
                    if (!$selectedConversationUser) {
                        $query->whereRaw('1 = 0');

                        return;
                    }

                    $query->where(
                        'conversation_user_id',
                        $selectedConversationUser->id
                    );
                }
            )
            ->withTrashed()
            ->orderBy('created_at', 'asc')
            ->get();

        /*
         * Informations réelles utilisées par le nouveau design
         * des groupes Arabe et Coran.
         *
         * Pas de faux statut « en ligne » : on affiche uniquement
         * les comptes réellement actifs (is_active).
         */
        $groupParticipants = collect();
        $groupParticipantsCount = 0;
        $groupActiveParticipantsCount = 0;
        $groupLastActivity = null;

        if (!$isAdministration) {
            $studentIds = DB::table('class_user')
                ->where(
                    'subject_id',
                    $subject->id
                )
                ->pluck('user_id');

            $professorIds = ProfAssignment::query()
                ->where(
                    'subject_id',
                    $subject->id
                )
                ->pluck('prof_id');

            $participantIds = $studentIds
                ->merge($professorIds)
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $groupParticipants = User::query()
                ->whereIn('id', $participantIds)
                ->whereIn('role', ['student', 'prof'])
                ->orderByRaw(
                    "CASE WHEN role = 'prof' THEN 0 ELSE 1 END"
                )
                ->orderBy('name')
                ->get();

            $groupParticipantsCount =
                $groupParticipants->count();

            $groupActiveParticipantsCount =
                $groupParticipants
                    ->filter(
                        fn (User $participant) =>
                            (bool) $participant->is_active
                    )
                    ->count();

            $groupLastActivity =
                $messages
                    ->sortByDesc('created_at')
                    ->first()
                    ?->created_at;
        }

        return view(
            'admin.chat',
            compact(
                'messages',
                'subject',
                'conversationUsers',
                'selectedConversationUser',
                'isAdministration',
                'conversationRole',
                'conversationSpaceLabel',
                'groupParticipants',
                'groupParticipantsCount',
                'groupActiveParticipantsCount',
                'groupLastActivity'
            )
        );
    }

    // Envoyer message admin
    public function adminSend(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'message' => ['required', 'string', 'max:5000'],
            'conversation_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $subject = Subject::findOrFail(
            $validated['subject_id']
        );

        abort_unless(
            $this->isAdministrationSubject(
                $subject
            )
            || $subject->status === 'active',
            422,
            'Cette matière n’est pas active pour les discussions.'
        );

        $conversationUserId = null;
        if ($this->isAdministrationSubject($subject)) {
            $request->validate(['conversation_user_id' => ['required', 'integer']]);
            abort_unless(User::whereKey($validated['conversation_user_id'])->whereIn('role', ['student', 'prof'])->exists(), 422);
            $conversationUserId = (int) $validated['conversation_user_id'];
        }

        Message::create([
            'user_id' => auth()->id(),
            'subject_id' => $subject->id,
            'conversation_user_id' => $conversationUserId,
            'message' => $validated['message'],
        ]);

        return back();
    }

    // Supprimer un message admin
    public function adminDelete(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
            'messages' => [
                'required',
                'array',
                'size:1',
            ],
            'messages.*' => [
                'required',
                'integer',
                'exists:messages,id',
            ],
        ], [
            'messages.required' =>
                'Sélectionnez le message à supprimer.',
            'messages.size' =>
                'Un seul message peut être supprimé à la fois.',
        ]);

        $messageId = (int) $validated['messages'][0];

        $message = Message::query()
            ->whereKey($messageId)
            ->where(
                'subject_id',
                $validated['subject_id']
            )
            ->firstOrFail();

        /*
         * Soft delete :
         * le message n'est pas supprimé physiquement de la base.
         * Il apparaît comme « Message supprimé » dans l'historique.
         */
        $message->delete();

        return back()->with(
            'success',
            'Le message a été supprimé.'
        );
    }

    /* =========================
        PROF
    ========================= */

    // Liste matières professeur
    public function profSubjects()
    {
        $assignments =
            app(
                ProfessorPathService::class
            )->assignments(
                auth()->id()
            );

        $subjects =
            $assignments
                ->map(
                    function (
                        ProfAssignment $assignment
                    ) {
                        $subject =
                            $assignment
                                ->subject;

                        if (
                            !$subject
                            || $subject->status
                                !== 'active'
                        ) {
                            return null;
                        }

                        $code =
                            app(
                                AssignmentScopeService::class
                            )->professorCode(
                                $assignment
                            );

                        if (!$code) {
                            return null;
                        }

                        $space =
                            clone $subject;

                        $space->setAttribute(
                            'assignment_code',
                            $code
                        );

                        $space->setAttribute(
                            'class_slot_id',
                            (int) $assignment
                                ->class_slot_id
                        );

                        return $space;
                    }
                )
                ->filter()
                ->unique(
                    fn ($subject) =>
                        $subject->id
                        . '|'
                        . $subject
                            ->assignment_code
                )
                ->values();

        $administration =
            Subject::query()
                ->where(
                    'name',
                    'Administration'
                )
                ->first();

        if ($administration) {
            $administration
                ->setAttribute(
                    'assignment_code',
                    null
                );

            $subjects =
                $subjects
                    ->push(
                        $administration
                    )
                    ->values();
        }

        return view(
            'prof.chat_subjects',
            compact('subjects')
        );
    }

    // Chat professeur
    public function profChat(Subject $subject)
    {
        $isAdministration =
            $this
                ->isAdministrationSubject(
                    $subject
                );

        $assignmentCode = null;

        if (!$isAdministration) {
            $assignmentCode =
                strtoupper(
                    trim(
                        (string)
                            request(
                                'assignment_code',
                                ''
                            )
                    )
                );

            abort_unless(
                $subject->status === 'active'
                && $this
                    ->profOwnsChatScope(
                        $subject,
                        $assignmentCode
                    ),
                403,
                'Ce groupe ne fait pas partie de vos affectations.'
            );
        }

        $messages =
            Message::query()
                ->where(
                    'subject_id',
                    $subject->id
                )
                ->when(
                    $isAdministration,
                    fn ($query) =>
                        $query->where(
                            'conversation_user_id',
                            auth()->id()
                        )
                )
                ->when(
                    !$isAdministration,
                    fn ($query) =>
                        $query->where(
                            'assignment_code',
                            $assignmentCode
                        )
                )
                ->with('user')
                ->whereNull('deleted_at')
                ->orderBy(
                    'created_at',
                    'asc'
                )
                ->get();

        $groupChatContext =
            $isAdministration
                ? $this
                    ->emptyGroupChatContext()
                : $this
                    ->groupChatContext(
                        $subject,
                        $messages,
                        $assignmentCode
                    );

        return view(
            'prof.chat',
            compact(
                'subject',
                'messages',
                'isAdministration',
                'groupChatContext',
                'assignmentCode'
            )
        );
    }

    // Envoyer message professeur
    public function profSend(Request $request)
    {
        $validated =
            $request->validate([
                'subject_id' => [
                    'required',
                    'integer',
                    'exists:subjects,id',
                ],
                'assignment_code' => [
                    'nullable',
                    'string',
                    'max:64',
                ],
                'message' => [
                    'required',
                    'string',
                    'max:5000',
                ],
            ]);

        $subject =
            Subject::findOrFail(
                $validated['subject_id']
            );

        $isAdministration =
            $this
                ->isAdministrationSubject(
                    $subject
                );

        $assignmentCode = null;
        $classSlotId = null;

        if (!$isAdministration) {
            $assignmentCode =
                strtoupper(
                    trim(
                        (string) (
                            $validated[
                                'assignment_code'
                            ]
                            ?? ''
                        )
                    )
                );

            $scope =
                $this
                    ->profChatScope(
                        $subject,
                        $assignmentCode
                    );

            abort_unless(
                $scope
                && $subject->status === 'active',
                403,
                'Ce groupe ne fait pas partie de vos affectations.'
            );

            $classSlotId =
                (int) $scope
                    ->class_slot_id;
        }

        Message::create([
            'user_id' =>
                auth()->id(),
            'subject_id' =>
                $subject->id,
            'conversation_user_id' =>
                $isAdministration
                    ? auth()->id()
                    : null,
            'assignment_code' =>
                $assignmentCode,
            'class_slot_id' =>
                $classSlotId ?: null,
            'message' =>
                $validated['message'],
        ]);

        return back();
    }

    // Supprimer messages professeur
    public function profDelete(Request $request)
    {
        $validated =
            $request->validate([
                'subject_id' => [
                    'required',
                    'integer',
                    'exists:subjects,id',
                ],
                'assignment_code' => [
                    'nullable',
                    'string',
                    'max:64',
                ],
                'messages' => [
                    'required',
                    'array',
                    'min:1',
                ],
                'messages.*' => [
                    'integer',
                    'exists:messages,id',
                ],
            ]);

        $subject =
            Subject::findOrFail(
                $validated['subject_id']
            );

        $isAdministration =
            $this
                ->isAdministrationSubject(
                    $subject
                );

        $assignmentCode =
            strtoupper(
                trim(
                    (string) (
                        $validated[
                            'assignment_code'
                        ]
                        ?? ''
                    )
                )
            );

        if (!$isAdministration) {
            abort_unless(
                $this
                    ->profOwnsChatScope(
                        $subject,
                        $assignmentCode
                    ),
                403
            );
        }

        Message::query()
            ->whereIn(
                'id',
                $validated['messages']
            )
            ->where(
                'subject_id',
                $subject->id
            )
            ->when(
                $isAdministration,
                fn ($query) =>
                    $query->where(
                        'conversation_user_id',
                        auth()->id()
                    )
            )
            ->when(
                !$isAdministration,
                fn ($query) =>
                    $query->where(
                        'assignment_code',
                        $assignmentCode
                    )
            )
            ->where(
                'user_id',
                auth()->id()
            )
            ->delete();

        return back();
    }

    /**
     * Contexte commun des chats de groupe Arabe / Coran.
     *
     * Le chat existant reste au niveau de la matière.
     * On n'élargit pas les droits d'accès : ces données sont
     * uniquement utilisées après les contrôles d'autorisation.
     */
    /**
     * Participants du groupe exact D1ARD1/L1ARD1/...
     */
    /**
     * Participants du groupe exact D1ARD1/L1ARD1/...
     */
    private function groupChatContext(
        Subject $subject,
        Collection $messages,
        ?string $assignmentCode = null
    ): array {
        $assignmentCode =
            strtoupper(
                trim(
                    (string) $assignmentCode
                )
            );

        if ($assignmentCode !== '') {
            $studentIds =
                DB::table('class_user')
                    ->where(
                        'subject_id',
                        $subject->id
                    )
                    ->where(
                        'student_slot_code',
                        $assignmentCode
                    )
                    ->pluck('user_id')
                    ->filter()
                    ->map(
                        fn ($id) =>
                            (int) $id
                    )
                    ->unique()
                    ->values();

            $professorIds =
                ProfAssignment::query()
                    ->with([
                        'subject',
                        'classRoom',
                        'classSlot',
                    ])
                    ->where(
                        'subject_id',
                        $subject->id
                    )
                    ->get()
                    ->filter(
                        fn (ProfAssignment $assignment) =>
                            strtoupper(
                                trim(
                                    (string)
                                        app(
                                            AssignmentScopeService::class
                                        )->professorCode(
                                            $assignment
                                        )
                                )
                            ) === $assignmentCode
                    )
                    ->pluck('prof_id')
                    ->filter()
                    ->map(
                        fn ($id) =>
                            (int) $id
                    )
                    ->unique()
                    ->values();
        } else {
            $studentIds =
                DB::table('class_user')
                    ->where(
                        'subject_id',
                        $subject->id
                    )
                    ->pluck('user_id');

            $professorIds =
                ProfAssignment::query()
                    ->where(
                        'subject_id',
                        $subject->id
                    )
                    ->pluck('prof_id');
        }

        $participantIds =
            collect($studentIds)
                ->merge(
                    $professorIds
                )
                ->filter()
                ->map(
                    fn ($id) =>
                        (int) $id
                )
                ->unique()
                ->values();

        $participants =
            User::query()
                ->whereIn(
                    'id',
                    $participantIds
                )
                ->whereIn(
                    'role',
                    [
                        'student',
                        'prof',
                    ]
                )
                ->orderByRaw(
                    "CASE WHEN role = 'prof' THEN 0 ELSE 1 END"
                )
                ->orderBy('name')
                ->get();

        $professors =
            $participants
                ->where(
                    'role',
                    'prof'
                )
                ->values();

        return [
            'participants_count' =>
                $participants->count(),
            'active_accounts_count' =>
                $participants
                    ->where(
                        'is_active',
                        true
                    )
                    ->count(),
            'students_count' =>
                $participants
                    ->where(
                        'role',
                        'student'
                    )
                    ->count(),
            'professors_count' =>
                $professors->count(),
            'professors' =>
                $professors,
            'recent_authors' =>
                $messages
                    ->sortByDesc(
                        'created_at'
                    )
                    ->pluck('user')
                    ->filter()
                    ->unique('id')
                    ->take(6)
                    ->values(),
            'last_activity' =>
                $messages
                    ->sortByDesc(
                        'created_at'
                    )
                    ->first()
                    ?->created_at,
        ];
    }

    private function profChatScope(
        Subject $subject,
        string $assignmentCode
    ): ?ProfAssignment {
        $assignmentCode =
            strtoupper(
                trim($assignmentCode)
            );

        if ($assignmentCode === '') {
            return null;
        }

        return app(
            ProfessorPathService::class
        )
            ->assignments(
                auth()->id()
            )
            ->first(
                fn (ProfAssignment $assignment) =>
                    (int) $assignment->subject_id
                        === (int) $subject->id
                    && strtoupper(
                        trim(
                            (string)
                                app(
                                    AssignmentScopeService::class
                                )->professorCode(
                                    $assignment
                                )
                        )
                    ) === $assignmentCode
            );
    }

    private function profOwnsChatScope(
        Subject $subject,
        string $assignmentCode
    ): bool {
        return
            $this
                ->profChatScope(
                    $subject,
                    $assignmentCode
                )
            !== null;
    }


    private function emptyGroupChatContext(): array
    {
        return [
            'participants_count' => 0,
            'active_accounts_count' => 0,
            'students_count' => 0,
            'professors_count' => 0,
            'professors' => collect(),
            'recent_authors' => collect(),
            'last_activity' => null,
        ];
    }

    private function authorizeProfSubject(
        Subject $subject
    ): void {
        abort_unless(
            $this->isAdministrationSubject(
                $subject
            )
            || (
                $subject->status === 'active'
                && ProfAssignment::query()
                    ->where(
                        'prof_id',
                        auth()->id()
                    )
                    ->where(
                        'subject_id',
                        $subject->id
                    )
                    ->exists()
            ),
            403,
            'Cette matière n’est pas disponible pour la discussion.'
        );
    }
}

