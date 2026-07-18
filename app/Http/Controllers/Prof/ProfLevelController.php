<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Course;
use Illuminate\Http\Request;

class ProfLevelController extends Controller
{
    /**
     * Affiche les cours d'une matière pour une classe (ancienne route browse, conservée pour rétrocompatibilité)
     */
    public function courses(Level $level, ClassRoom $class, Subject $subject)
    {
        $courses = Course::where('subject_id', $subject->id)
            ->where('class_id', $class->id)
            ->where('user_id', auth()->id())
            ->with(['classRoom', 'subject'])
            ->get();

        return view('prof.levels.courses', compact('level', 'class', 'subject', 'courses'));
    }
}
