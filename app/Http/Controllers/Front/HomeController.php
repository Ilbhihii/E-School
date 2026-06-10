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

        return view('front.home', compact('classes','courses','lives'));
    }

    // Liste classes
    public function classes()
    {
        $subjects = \App\Models\Subject::all();

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
