<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Message;
use App\Models\User;
use App\Models\ProfAssignment;

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

        $subjectId = $this->assignedStudentSubjectId($user->id);
        $subjects = $subjectId ? Subject::whereKey($subjectId)->get() : collect();
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
        abort_unless($isAdministration || (int) $this->assignedStudentSubjectId($user->id) === (int) $subject->id, 403, 'Cette matière ne fait pas partie de votre programme.');

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
        abort_unless($isAdministration || (int) $this->assignedStudentSubjectId($user->id) === (int) $subject->id, 403);

        Message::create([
            'user_id' => $user->id,
            'subject_id' => $validated['subject_id'],
            'conversation_user_id' => $isAdministration ? $user->id : null,
            'message' => $validated['message'],
        ]);

        return back();
    }

    private function assignedStudentSubjectId(int $userId): ?int
    {
        $subjectId = \DB::table('class_user')
            ->where('user_id', $userId)
            ->whereNotNull('subject_id')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->value('subject_id');

        return $subjectId ? (int) $subjectId : null;
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
        abort_unless(in_array($subject->name, ['Arabe', 'Coran', 'Administration'], true), 404);

        $isAdministration = $this->isAdministrationSubject($subject);
        $messages = Message::with(['user', 'conversationUser'])
            ->where('subject_id', $subject->id)
            ->withTrashed()
            ->orderBy('created_at', 'asc')
            ->get();

        $conversationUsers = $isAdministration
            ? User::whereIn('role', ['student', 'prof'])->orderBy('role')->orderBy('name')->get()
            : collect();

        return view('admin.chat', compact('messages', 'subject', 'conversationUsers', 'isAdministration'));
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

    // Supprimer messages admin
    public function adminDelete(Request $request)
    {
        if ($request->has('messages')) {

            Message::whereIn('id', $request->messages)
                ->delete();

            return back()->with('success', 'Messages supprimés !');
        }

        return back();
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

