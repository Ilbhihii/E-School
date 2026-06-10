<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Live;
use App\Models\User;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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

        $testResultsCount = \App\Models\Result::count();

$students = User::where('role','student')->paginate(10);

        return view('admin.dashboard', compact(
        'classesCount',
        'coursesCount',
        'livesCount',
        'usersCount',
        'testResultsCount',
        'students'

        ));


    }

    public function absences(\Illuminate\Http\Request $request)
    {
        $query = Absence::with(['user.classRoom']);

        // Search
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%');
            })->orWhere('date', 'like', '%'.$request->search.'%');
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        // Sort
        $sort = $request->get('sort', 'date');
        $dir = $request->get('dir', 'desc');
        $query->orderBy($sort, $dir);

        $absences = $query->paginate(20);
        $classes = \App\Models\ClassRoom::all();

        return view('admin.absences', compact('absences', 'classes'));
    }

    public function show(Absence $absence)
    {
        $absence->load('user.classRoom');
        return view('admin.absences.show', compact('absence'));
    }

    public function edit(Absence $absence)
    {
        return view('admin.absences.edit', compact('absence'));
    }

    public function update(\Illuminate\Http\Request $request, Absence $absence)
    {
        $request->validate([
            'date' => 'required|date',
            'present' => 'boolean'
        ]);

        $absence->update($request->only('date', 'present'));

        return redirect()->route('admin.absences')->with('success', 'Absence modifiée');
    }

    public function destroy(Absence $absence)
    {
        $absence->delete();
        return back()->with('success', 'Absence supprimée');
    }

    public function create(Request $request)
    {
        $classId = $request->get('class_id');
        $classes = \App\Models\ClassRoom::all();
        $students = collect();

        if ($classId) {
            $students = \App\Models\User::where('role', 'student')
                ->where('class_id', $classId)
                ->get();
        }

        return view('admin.absences.create', compact('classes', 'students', 'classId'));
    }

    public function store(Request $request)
    {
        if(!in_array(auth()->user()->role, ['admin','prof'])){
            abort(403);
        }

        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'present' => 'required|boolean'
        ]);

        \App\Models\Absence::create($request->only('user_id', 'date', 'present'));

        $classId = $request->class_id;
        return redirect()->route('admin.absences', ['class_id' => $classId])->with('success', 'Absence enregistrée avec succès');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = auth()->user();

        // Upload image
        if ($request->hasFile('profile_photo')) {
            // delete old
            if ($user->profile_photo) {
                Storage::delete('public/' . $user->profile_photo);
            }

            $path = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo = $path;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Profil mis à jour !');
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

