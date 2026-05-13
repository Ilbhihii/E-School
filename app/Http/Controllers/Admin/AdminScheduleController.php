<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ClassRoom;
use App\Models\User;
use App\Models\Subject;

class AdminScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['classRoom', 'prof'])->get();
        $classes = ClassRoom::all();
        $teachers = User::where('role', 'prof')->get();
        $subjects = Subject::all();

        return view('admin.schedule.index', compact('schedules', 'classes', 'teachers', 'subjects'));
    }

    public function store(Request $request)
    {
        if(!in_array(auth()->user()->role, ['admin','prof'])){
            abort(403);
        }

        $request->validate([
            'prof_id' => 'required|integer|exists:users,id',
            'class_id' => 'required|integer|exists:class_rooms,id',
            'subject' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        Schedule::create([
            'prof_id' => $request->prof_id,
            'class_id' => $request->class_id,
            'subject' => $request->subject,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return back()->with('success', 'Planning ajouté');
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();

        return back()->with('success', 'Planning supprimé');
    }
}

