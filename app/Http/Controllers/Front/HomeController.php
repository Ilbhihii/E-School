<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Live;

class HomeController extends Controller
{
    // Accueil
    public function index()
    {
        $classes = ClassRoom::count();
        $courses = Course::count();
        $lives = Live::latest()->take(3)->get();

        $subjectsReligieux = \App\Models\Subject::where('name', 'Coran')->get();
        $subjectsScolaire = \App\Models\Subject::where('name', 'Arabe')->get();

$subjectsGrouped = [
            'Matières Religieuses' => [
                'subjects' => $subjectsReligieux,
                'color' => 'primary'
            ],
            'Matières Scolaires' => [
                'subjects' => $subjectsScolaire,
                'color' => 'success'
            ]
        ];

        return view('front.home', compact('classes','courses','lives', 'subjectsGrouped'));
    }

    // Liste des niveaux
    public function niveaux()
    {
        $levels = \App\Models\Level::withCount(['classes as class_count'])
            ->with(['classes' => function($q) {
                $q->withCount('subjects');
            }])
            ->orderBy('order')
            ->get();

        $totalClasses = $levels->sum('class_count');
        $totalCourses = \App\Models\Course::count();

        return view('front.public-levels', compact('levels', 'totalClasses', 'totalCourses'));
    }

    // Liste des matières
    public function classes()
    {
        $subjects = \App\Models\Subject::with(['classes.level', 'levels'])
            ->withCount(['courses', 'classes'])
            ->whereIn('name', ['Arabe', 'Coran'])
            ->get();

        $subjects->each(function ($subject) {
            $classLevels = $subject->classes
                ->pluck('level')
                ->filter();

            $subject->available_levels = $subject->levels
                ->concat($classLevels)
                ->unique('id')
                ->sortBy('order')
                ->values();

            $hasLevels = $subject->available_levels->isNotEmpty();
            $hasClasses = $subject->classes_count > 0;

            if ($hasLevels && $hasClasses) {
                $subject->status = 'disponible';
                $subject->status_label = 'Disponible';
                $subject->status_icon = 'bi-check-circle-fill';
                $subject->status_color = '#4ADE80';
                $subject->status_bg = 'rgba(34,197,94,0.15)';
                $subject->status_border = 'rgba(34,197,94,0.2)';
            } elseif ($hasLevels) {
                $subject->status = 'en_cours';
                $subject->status_label = 'En cours';
                $subject->status_icon = 'bi-hourglass-split';
                $subject->status_color = '#FB923C';
                $subject->status_bg = 'rgba(251,146,60,0.15)';
                $subject->status_border = 'rgba(251,146,60,0.2)';
            } else {
                $subject->status = 'non_disponible';
                $subject->status_label = 'Non disponible';
                $subject->status_icon = 'bi-x-circle-fill';
                $subject->status_color = '#FCA5A5';
                $subject->status_bg = 'rgba(239,68,68,0.15)';
                $subject->status_border = 'rgba(239,68,68,0.2)';
            }
        });

        return view('front.classes', compact('subjects'));
    }

    // Cours d'une classe
    public function classCourses($id)
    {
$class = \App\Models\ClassRoom::with(['courses', 'subjects'])->findOrFail($id);
        $courses = $class->courses;
        return view('front.class-courses', compact('class', 'courses'));
    }

    // Détail cours
    public function courseShow($id)
    {
        $course = Course::findOrFail($id);
        return view('front.course-show', compact('course'));
    }

    // Toutes les classes avec leurs cours
    public function allClassesCourses()
    {
$classes = \App\Models\ClassRoom::with(['courses', 'subjects'])->get();
        return view('front.all-classes-courses', compact('classes'));
    }

    // Lives
    public function lives()
    {
        $lives = Live::latest()->get();
        return view('front.lives', compact('lives'));
    }
}
