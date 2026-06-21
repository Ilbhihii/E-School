<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Level;
use App\Models\Course;

class FrontController extends Controller
{
    public function subjects()
    {
        $subjectsReligieux = Subject::where('type', 'religieux')->get();
        $subjectsScolaire = Subject::where('type', 'scolaire')->get();
        return view('front.classes', compact('subjectsReligieux', 'subjectsScolaire'));
    }

    public function subjectClasses($id)
    {
        $subject = Subject::with('classes')->findOrFail($id);
        return view('front.subject-classes', compact('subject'));
    }

    public function subjectLevels($id)
    {
        $subject = Subject::findOrFail($id);

        // Les niveaux sont reliés aux matières via : Subject → classes (pivot) → ClassRoom.level_id → Level
        $levelIds = $subject->classes()->pluck('class_rooms.level_id')->unique();
        $levels = Level::whereIn('id', $levelIds)->get();

        return view('front.subject-levels', compact('subject', 'levels'));
    }

    public function levelClasses($subjectId, $levelId)
    {
        $subject = Subject::findOrFail($subjectId);
        $level = Level::findOrFail($levelId);
        $classes = \App\Models\ClassRoom::where('level_id', $levelId)
            ->whereHas('subjects', fn($q) => $q->where('subject_id', $subjectId))
            ->get();

        return view('front.level-classes', compact('subject', 'level', 'classes'));
    }

    public function levelCourses($id)
    {
        $level = Level::with('courses')->findOrFail($id);

        return view('front.level-courses', compact('level'));
    }

    public function showCourse($id)
    {
        $course = Course::with('learningTests')->findOrFail($id);

        return view('front.course-show', compact('course'));
    }

    public function courses($subject_id, $level_id, $class_id)
    {
        $courses = Course::where('subject_id', $subject_id)
            ->where('level_id', $level_id)
            ->where('class_id', $class_id)
            ->get();

        return view('front.class-courses', compact('courses'));
    }

    public function religieux()
    {
        $subjects = \App\Models\Subject::where('type', 'religieux')->get();

        return view('front.religieux', compact('subjects'));
    }
}
