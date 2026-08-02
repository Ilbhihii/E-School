<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Message;
use App\Models\User;
use App\Models\ProfAssignment;
use App\Services\LearningPathService;
use Illuminate\Support\Collection;

class ChatController extends Controller
{
    /* =========================
        STUDENT
    ========================= */

    // Liste des matières (filtrée par classe pour les étudiants)
    public function subjects()
    {
        $user = auth()->user();
        abort_unless($user->isStudent(), 403);

        $subjectIds = $this->assignedStudentSubjectIds($user->id);

        $subjects = Subject::query()
            ->whereIn('id', $subjectIds)
            ->orderBy('name')
            ->get();

        $administration = Subject::where('name', 'Administration')->first();
        if ($administration) {
            $subjects = $subjects->push($administration)->unique('id')->values();
        }

        return view('student.chats', compact('subjects'));
    }

    // Chat pour une matière (used by route:chat) — accès vérifié pour les étudiants
    public function index($subject_id)
    {
        $subject = Subject::findOrFail($subject_id);
        $user = auth()->user();

        abort_unless($user->isStudent(), 403);
        $isAdministration = $this->isAdministrationSubject($subject);
        $assignedSubjectIds = $this->assignedStudentSubjectIds($user->id);

        abort_unless(
            $isAdministration
            || $assignedSubjectIds->contains((int) $subject->id),
            403,
            'Cette matière ne fait pas partie de votre programme.'
        );

        $messages = Message::where('subject_id', $subject_id)
            ->when($isAdministration, fn($query) => $query->where('conversation_user_id', $user->id))
            ->with('user')
            ->latest()
            ->get();

        return view('student.chat', compact('subject', 'messages'));
    }

    // Envoyer message étudiant
    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $user = auth()->user();
        abort_unless($user->isStudent(), 403);
        $subject = Subject::findOrFail($validated['subject_id']);
        $isAdministration = $this->isAdministrationSubject($subject);
        $assignedSubjectIds = $this->assignedStudentSubjectIds($user->id);

        abort_unless(
            $isAdministration
            || $assignedSubjectIds->contains((int) $subject->id),
            403
        );

        Message::create([
            'user_id' => $user->id,
            'subject_id' => $validated['subject_id'],
            'conversation_user_id' => $isAdministration ? $user->id : null,
            'message' => $validated['message'],
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

    // Liste des matières pour admin (adminIndex route)
    public function adminIndex()
    {
        $allowedSubjects = ['Arabe', 'Coran', 'Administration'];

        $subjects = Subject::whereIn('name', $allowedSubjects)
            ->withCount(['messages' => function($query) {
            $query->whereNull('deleted_at');
        }])
            ->with(['messages' => function($query) {
                $query->whereNull('deleted_at')->latest()->limit(1);
            }])
            ->get()
            ->unique('name')
            ->sortBy(fn($subject) => array_search($subject->name, $allowedSubjects, true))
            ->values();

        return view('admin.chat-list', compact('subjects'));
    }

    // Chat admin pour une matière
    public function adminChat($subject)
    {
        $subject = Subject::findOrFail($subject);

        abort_unless(
            in_array(
                $subject->name,
                [
                    'Arabe',
                    'Coran',
                    'Administration',
                ],
                true
            ),
            404
        );

        $isAdministration =
            $this->isAdministrationSubject($subject);

        $conversationUsers = collect();
        $selectedConversationUser = null;

        if ($isAdministration) {
            /*
             * Construire la liste des conversations privées
             * Administration ↔ étudiant/professeur.
             */
            $conversationUsers = User::query()
                ->whereIn(
                    'role',
                    ['student', 'prof']
                )
                ->orderByRaw(
                    "CASE
                        WHEN role = 'student' THEN 1
                        WHEN role = 'prof' THEN 2
                        ELSE 3
                    END"
                )
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

            $selectedConversationUserId =
                (int) request('student');

            if ($selectedConversationUserId > 0) {
                $selectedConversationUser =
                    $conversationUsers->firstWhere(
                        'id',
                        $selectedConversationUserId
                    );
            }

            /*
             * En l'absence de paramètre, ouvrir d'abord une
             * conversation existante, sinon le premier utilisateur.
             */
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

        return view(
            'admin.chat',
            compact(
                'messages',
                'subject',
                'conversationUsers',
                'selectedConversationUser',
                'isAdministration'
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

        $subject = Subject::findOrFail($validated['subject_id']);
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
        $subjectIds = ProfAssignment::where('prof_id', auth()->id())->pluck('subject_id');
        $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();
        $administration = Subject::where('name', 'Administration')->first();
        if ($administration) {
            $subjects = $subjects->push($administration)->unique('id')->values();
        }
        return view('prof.chat_subjects', compact('subjects'));
    }

    // Chat professeur
    public function profChat(Subject $subject)
    {
        $this->authorizeProfSubject($subject);
        $isAdministration = $this->isAdministrationSubject($subject);
        $messages = $subject->messages()->with('user')
            ->when($isAdministration, fn ($query) => $query->where('conversation_user_id', auth()->id()))
            ->whereNull('deleted_at')->orderBy('created_at', 'asc')->get();
        return view('prof.chat', compact('subject', 'messages', 'isAdministration'));
    }

    // Envoyer message professeur
    public function profSend(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'message' => ['required', 'string', 'max:5000'],
        ]);
        $subject = Subject::findOrFail($validated['subject_id']);
        $this->authorizeProfSubject($subject);

        Message::create([
            'user_id' => auth()->id(),
            'subject_id' => $subject->id,
            'conversation_user_id' => $this->isAdministrationSubject($subject) ? auth()->id() : null,
            'message' => $validated['message'],
        ]);

        return back();
    }

    // Supprimer messages professeur
    public function profDelete(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'messages' => ['required', 'array', 'min:1'],
            'messages.*' => ['integer', 'exists:messages,id'],
        ]);
        $subject = Subject::findOrFail($validated['subject_id']);
        $this->authorizeProfSubject($subject);
        Message::whereIn('id', $validated['messages'])
            ->where('subject_id', $subject->id)
            ->when($this->isAdministrationSubject($subject), fn ($query) => $query->where('conversation_user_id', auth()->id()))
            ->where('user_id', auth()->id())
            ->delete();

        return back();
    }

    private function authorizeProfSubject(Subject $subject): void
    {
        abort_unless($this->isAdministrationSubject($subject)
            || ProfAssignment::where('prof_id', auth()->id())
                ->where('subject_id', $subject->id)->exists(), 403);
    }
}

