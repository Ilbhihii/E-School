<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\User;
use App\Models\Subject;

class AdminScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['classRoom.level', 'prof'])->orderBy('date', 'desc')->orderBy('start_time', 'asc')->get();
        $levels = Level::orderBy('name')->get();
        $classes = ClassRoom::with('level')->get();
        $teachers = User::where('role', 'prof')->get();
        $subjects = Subject::all();

        return view('admin.schedule.index', compact('schedules', 'levels', 'classes', 'teachers', 'subjects'));
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
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time'
        ]);

        Schedule::create([
            'prof_id' => $request->prof_id,
            'class_id' => $request->class_id,
            'subject' => $request->subject,
            'date' => $request->date,
            'start_time' => $request->date . ' ' . $request->start_time . ':00',
            'end_time' => $request->date . ' ' . $request->end_time . ':00',
        ]);

        return back()->with('success', 'Planning ajouté');
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();

        return back()->with('success', 'Planning supprimé');
    }
}

