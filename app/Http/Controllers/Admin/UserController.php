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
     * Affiche la page d'assignation des professeurs (niveau + classe + matière)
     */
    public function profAssignments()
    {
        $professors = User::where('role', 'prof')->orderBy('name')->get();
        $levels = Level::orderBy('name')->get();
        $classes = ClassRoom::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $assignments = ProfAssignment::with(['prof', 'level', 'classRoom', 'subject'])
            ->latest()
            ->get();

        return view('admin.prof-assignments', compact(
            'professors', 'levels', 'classes', 'subjects', 'assignments'
        ));
    }

    /**
     * Enregistrer une nouvelle assignation de professeur
     */
    public function storeProfAssignment(Request $request)
    {
        $request->validate([
            'prof_id' => 'required|exists:users,id',
            'level_id' => 'required|exists:levels,id',
            'class_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Vérifier l'unicité
        $exists = ProfAssignment::where([
            'prof_id' => $request->prof_id,
            'level_id' => $request->level_id,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
        ])->exists();

        if ($exists) {
            return back()->with('error', 'Cette assignation existe déjà pour ce professeur.');
        }

        ProfAssignment::create($request->only([
            'prof_id', 'level_id', 'class_id', 'subject_id'
        ]));

        return back()->with('success', 'Assignation du professeur enregistrée avec succès.');
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
        $user = User::findOrFail($id);
        $user->is_active = true;
        $user->test_passed = true;
        $user->is_paid = true;
        $user->save();

        // Send activation email
        try {
            Mail::to($user->email)->send(new AccountActivatedMailable($user));
        } catch (\Exception $e) {
            \Log::error('Failed to send activation email to ' . $user->email . ': ' . $e->getMessage());
        }

        return back()->with('success', 'Compte activé et test validé avec succès. Email envoyé à ' . $user->email);
    }

    public function deactivate($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = false;
        $user->save();
        
        return redirect()->back()->with('success', 'Compte désactivé avec succès.');
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
     * Show class assignment management page
     */
    public function assignClass()
    {
        $students = User::where('role', 'student')->get();
        $classRooms = ClassRoom::with('level')->get();
        $levels = Level::orderBy('name')->get();

        // Get assignments: students assigned to classes
        $assignments = DB::table('class_user')
            ->join('users', 'class_user.user_id', '=', 'users.id')
            ->join('class_rooms', 'class_user.class_id', '=', 'class_rooms.id')
            ->select('class_user.id as pivot_id', 'class_user.user_id', 'class_user.class_id', 
                     'users.name as student_name', 'class_rooms.name as class_name')
            ->get();

        return view('admin.assign-class', compact('students', 'classRooms', 'levels', 'assignments'));
    }

    /**
     * Store new student-class assignment
     */
    public function storeAssignment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:class_user,user_id',
            'class_id' => 'required|exists:class_rooms,id'
        ]);

        DB::table('class_user')->insert([
            'user_id' => $request->user_id,
            'class_id' => $request->class_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 🔥 Synchroniser users.class_id pour que $user->classRoom() fonctionne
        User::where('id', $request->user_id)->update(['class_id' => $request->class_id]);

        return redirect()->back()->with('success', 'Étudiant assigné à la classe avec succès!');
    }

    /**
     * Update student-class assignment
     */
    public function updateAssignment(Request $request, $pivotId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:class_rooms,id'
        ]);

        DB::table('class_user')
            ->where('id', $pivotId)
            ->update([
                'user_id' => $request->user_id,
                'class_id' => $request->class_id,
                'updated_at' => now()
            ]);

        // 🔥 Synchroniser users.class_id
        User::where('id', $request->user_id)->update(['class_id' => $request->class_id]);

        return redirect()->back()->with('success', 'Assignation modifiée avec succès!');
    }

    /**
     * Delete student-class assignment
     */
    public function destroyAssignment($pivotId)
    {
        $pivot = DB::table('class_user')->where('id', $pivotId)->first();

        DB::table('class_user')
            ->where('id', $pivotId)
            ->delete();

        // 🔥 Effacer users.class_id si c'était la seule assignation
        if ($pivot) {
            User::where('id', $pivot->user_id)->update(['class_id' => null]);
        }

        return redirect()->back()->with('success', 'Assignation supprimée avec succès!');
    }
}
