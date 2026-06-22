<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Classe;
use App\Models\Level;
use App\Models\User;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    // ✅ LISTE DES CLASSES

    public function index()
    {
        $classes = ClassRoom::all();

        return view('admin.classes.index', compact('classes'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        // Insert into pivot table
        \DB::table('class_user')->updateOrInsert(
            ['class_id' => $request->class_id, 'user_id' => $request->user_id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        return back()->with('success','Étudiant assigné à la classe (pivot)');
    }

public function assignForm()
    {
        $students = User::where('role','student')->get();
        $classRooms = ClassRoom::all();
        $assignments = DB::table('class_user')
            ->join('users', 'class_user.user_id', '=', 'users.id')
            ->join('class_rooms', 'class_user.class_id', '=', 'class_rooms.id')
            ->select(
                'class_user.id as pivot_id',
                'class_user.user_id',
                'class_user.class_id',
                'users.name as student_name',
                'class_rooms.name as class_name'
            )
            ->orderBy('users.name')
            ->get();

        return view('admin.assign-class', compact('students', 'classRooms', 'assignments'));
    }


    // ✅ Classes list for admin.classes.index
    public function classesIndex()
    {
        $classes = ClassRoom::all();

        return view('admin.classes.index', compact('classes'));
    }

    // ✅ FORMULAIRE CREATE
    public function create(Request $request)
    {
        $levels = Level::all();
        $selectedLevelId = $request->get('level_id');
        return view('admin.classes.create', compact('levels', 'selectedLevelId'));
    }

    // ✅ ENREGISTREMENT
    public function store(Request $request)
    {
        if(!in_array(auth()->user()->role, ['admin','prof'])){
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'level_id' => 'required|exists:levels,id',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $class = ClassRoom::create([
            'name' => $request->name,
            'level_id' => $request->level_id,
        ]);

        // Lier la classe à la matière via la table pivot
        if ($request->subject_id) {
            $class->subjects()->syncWithoutDetaching([$request->subject_id]);
        }

        if ($request->has('subject_id') && $request->subject_id) {
            return back()->with('success', 'Classe ajoutée et liée à la matière avec succès');
        }

        return redirect()->route('admin.classes.index')
                         ->with('success', 'Classe ajoutée avec succès');
    }

    // ✅ FORMULAIRE EDIT
    public function edit(ClassRoom $class)
    {
        $levels = Level::all();
        return view('admin.classes.edit', compact('class', 'levels'));
    }

    // ✅ UPDATE
    public function update(Request $request, ClassRoom $class)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level_id' => 'required|exists:levels,id',
        ]);

        $class->update([
            'name' => $request->name,
            'level_id' => $request->level_id,
        ]);

        return redirect()->route('admin.classes.index')
                         ->with('success', 'Classe modifiée avec succès');
    }

    // ✅ DELETE
    public function destroy(ClassRoom $class)
    {
        $class->delete();

        return redirect()->route('admin.classes.index')
                         ->with('success', 'Classe supprimée');
    }

    public function assignStudent(Request $request)
    {
        $user = User::find($request->user_id);
        $user->class_id = $request->class_id;
        $user->save();
        return back()->with('success','Étudiant assigné');
    }

    public function assignStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:class_rooms,id',
        ]);

        // Debug: dd($request->all()); // Remove after testing

        DB::table('class_user')->updateOrInsert(
            ['class_id' => $request->class_id, 'user_id' => $request->user_id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        return back()->with('success','Étudiant assigné à la classe');
    }

    public function updateAssignment(Request $request, $pivot_id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:class_rooms,id',
        ]);

        DB::table('class_user')
            ->where('id', $pivot_id)
            ->update([
                'user_id' => $request->user_id,
                'class_id' => $request->class_id,
                'updated_at' => now()
            ]);

        return back()->with('success', 'Assignation modifiée avec succès');
    }

    public function destroyAssignment($pivot_id)
    {
        \DB::table('class_user')->where('id', $pivot_id)->delete();

        return back()->with('success', 'Assignation supprimée');
    }


}
