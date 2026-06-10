<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Level;
use App\Models\Course;

class FrontController extends Controller
{
    public function subjectClasses($id)
    {
        $subject = Subject::with('classes')->findOrFail($id);
        return view('front.subject-classes', compact('subject'));
    }

    public function subjectLevels($id)
    {
        $subject = Subject::with('levels')->findOrFail($id);

        return view('front.subject-levels', compact('subject'));
    }

    public function levelCourses($id)
    {
        $level = Level::with('courses')->findOrFail($id);

        return view('front.level-courses', compact('level'));
    }

    public function showCourse($id)
    {
        $course = Course::with(['learningTests', 'subject', 'classRoom'])->findOrFail($id);

        return view('front.course-show', compact('course'));
    }

    public function courses($subject_id, $class_id)
    {
        $subject = Subject::findOrFail($subject_id);
        $classe = \App\Models\ClassRoom::findOrFail($class_id);

        $courses = Course::where('subject_id', $subject_id)
            ->where('class_id', $class_id)
            ->get();

        return view('front.class-courses', compact('courses', 'subject', 'classe'));
    }

    public function religieux()
    {
        $courses = Course::whereHas('subject', function ($q) {
            $q->where('type', 'religieux');
        })->get();

        return view('front.religieux', compact('courses'));
    }
}
