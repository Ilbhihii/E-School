<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ClassRoom;

class ScheduleController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::all();
        return view('prof.schedule', compact('classes'));
    }

    public function classes()
    {
        $classes = ClassRoom::all(['id', 'name']);
        return response()->json($classes);
    }

    public function store(Request $request)
    {
        if(!in_array(auth()->user()->role, ['admin','prof'])){
            abort(403);
        }

        $request->validate([
            'class_id' => 'required|integer|exists:class_rooms,id',
            'subject' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        Schedule::create([
            'prof_id' => auth()->id(),
            'class_id' => $request->class_id,
            'subject' => $request->subject,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return back()->with('success', 'Planning ajouté');
    }

    public function data(Request $request)
    {
        $user = auth()->user();

        $query = Schedule::where('prof_id', $user->id);

        if($request->class_id){
            $query->where('class_id', $request->class_id);
        }

        $events = $query->get()->map(function ($item) {
            $className = $item->classRoom ? $item->classRoom->name : 'N/A';
            return [
                'id' => $item->id,
                'title' => $item->subject . ' (' . $className . ')',
                'start' => $item->start_time->toISOString(),
                'end' => $item->end_time->toISOString(),
                'subject' => $item->subject
            ];
        });

        return response()->json($events);
    }

    public function update(Request $request)
    {
        $schedule = Schedule::find($request->id);

        $schedule->update([
            'start_time' => $request->start,
            'end_time' => $request->end,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}

