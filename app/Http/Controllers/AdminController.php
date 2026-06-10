<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absence;
use App\Models\Course;
use App\Models\User;
use App\Models\ClassRoom;

class AdminController extends Controller
{


    public function absenceStats()
    {

    $students = User::where('role','student')->get();

    $stats = [];

    foreach($students as $student){

    $absences = Absence::where('user_id',$student->id)
    ->where('present',0)
    ->count();

    $stats[] = [

    'name'=>$student->name,
    'absences'=>$absences

    ];

    }

    return view('admin.absence_stats',compact('stats'));

    }

    public function edit($id)
    {
        $class = ClassRoom::findOrFail($id);

        return view('admin.classes.edit', compact('class'));
    }

    public function profile()
    {
        return view('admin.profile', [
            'coursesCount' => Course::count(),
            'usersCount' => User::count(),
            'classesCount' => ClassRoom::count(),
        ]);
    }
}
