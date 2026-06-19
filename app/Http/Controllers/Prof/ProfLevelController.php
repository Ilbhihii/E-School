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
     * Affiche tous les niveaux (entrée de la navigation hiérarchique pour le prof)
     */
    public function index()
    {
        $levels = Level::with('classes')->latest()->get();

        return view('prof.levels.index', compact('levels'));
    }

    /**
     * Affiche les classes d'un niveau spécifique
     */
    public function classes(Level $level)
    {
        $classes = ClassRoom::where('level_id', $level->id)->with('subjects')->get();

        return view('prof.levels.classes', compact('level', 'classes'));
    }

    /**
     * Affiche les matières associées à une classe dans un niveau
     */
    public function subjects(Level $level, ClassRoom $class)
    {
        $subjects = Subject::whereHas('classes', function($q) use ($class) {
            $q->where('class_room_id', $class->id);
        })->withCount(['courses as course_count' => function($q) use ($class) {
            $q->where('class_id', $class->id);
        }])->get();

        return view('prof.levels.subjects', compact('level', 'class', 'subjects'));
    }

    /**
     * Affiche les cours d'une matière pour une classe spécifique
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
