<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Message;

class ChatController extends Controller
{
    /* =========================
        STUDENT
    ========================= */

    // Liste des matières
    public function subjects()
    {
$subjects = Subject::all()->unique('name');
        return view('student.chats', compact('subjects'));
    }

    // Chat pour une matière (used by route:chat)
public function index($subject_id)
    {
        $subject = Subject::findOrFail($subject_id);

        $messages = Message::where('subject_id', $subject_id)
            ->with('user')
            ->latest()
            ->get();

        return view('student.chat', compact('subject', 'messages'));
    }

    // Envoyer message étudiant
public function send(Request $request)
    {
        Message::create([
            'user_id' => auth()->id(),
            'subject_id' => $request->subject_id,
            'message' => $request->message,
        ]);

        return back();
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
        $subjects = Subject::withCount(['messages' => function($query) {
            $query->whereNull('deleted_at');
        }])
            ->with(['messages' => function($query) {
                $query->whereNull('deleted_at')->latest()->limit(1);
            }])
            ->get();

        $subjects = $subjects->unique('name');
        return view('admin.chat-list', compact('subjects'));
    }

    // Chat admin pour une matière
    public function adminChat($subject)
    {
        $subject = Subject::findOrFail($subject);

        $messages = Message::with('user')
            ->where('subject_id', $subject->id)
            ->withTrashed()
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.chat', compact('messages', 'subject'));
    }

    // Envoyer message admin
    public function adminSend(Request $request)
    {
        $request->validate([
            'subject_id' => 'required',
            'message' => 'required'
        ]);

        Message::create([
            'user_id' => auth()->id(),
            'subject_id' => $request->subject_id,
            'message' => $request->message
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
        $subjects = Subject::all()->unique('name');
        return view('prof.chat_subjects', compact('subjects'));
    }

    // Chat professeur
    public function profChat(Subject $subject)
    {
        $messages = $subject->messages()->with('user')->whereNull('deleted_at')->orderBy('created_at', 'asc')->get();
        return view('prof.chat', compact('subject', 'messages'));
    }

    // Envoyer message professeur
    public function profSend(Request $request)
    {
        $request->validate([
            'subject_id' => 'required',
            'message' => 'required'
        ]);

        Message::create([
            'user_id' => auth()->id(),
            'subject_id' => $request->subject_id,
            'message' => $request->message
        ]);

        return back();
    }

    // Supprimer messages professeur
    public function profDelete(Request $request)
    {
        if ($request->has('messages')) {

            Message::whereIn('id', $request->messages)
                ->where('subject_id', $request->subject_id)
                ->delete();
        }

        return back();
    }
}

