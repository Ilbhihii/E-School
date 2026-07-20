<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Level;
use App\Models\Course;
use App\Models\ClassRoom;

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
        $subject = Subject::withCount(['courses', 'classes'])->findOrFail($id);

        // La matière est désormais la source de vérité du niveau. Cela permet
        // aussi d'afficher les niveaux qui n'ont pas encore de classe.
        $levels = Level::where('subject_id', $subject->id)
            ->withCount([
                'courses' => fn($query) => $query->where('subject_id', $subject->id),
                'classes as available_classes_count' => fn($query) => $query
                    ->whereHas('subjects', fn($subjectQuery) => $subjectQuery
                        ->where('subjects.id', $subject->id)),
            ])
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        // Autres matières de la même famille (même type : religieux / scolaire)
        $sameFamilySubjects = Subject::where('type', $subject->type)
            ->where('id', '!=', $subject->id)
            ->whereIn('name', ['Arabe', 'Coran'])
            ->withCount('courses')
            ->get();

        return view('front.subject-levels', compact('subject', 'levels', 'sameFamilySubjects'));
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
        $course = Course::with(['learningTests', 'subject', 'level', 'classRoom'])->findOrFail($id);

        // Autres matières de la même famille (même type : religieux / scolaire)
        $sameFamilySubjects = collect([]);
        if ($course->subject) {
            $sameFamilySubjects = Subject::where('type', $course->subject->type)
                ->where('id', '!=', $course->subject->id)
                ->withCount('courses')
                ->limit(4)
                ->get();
        }

        return view('front.course-show', compact('course', 'sameFamilySubjects'));
    }

    public function courses($subject_id, $level_id, $class_id)
    {
        $class = ClassRoom::whereKey($class_id)
            ->where('level_id', $level_id)
            ->whereHas('subjects', fn($query) => $query->where('subjects.id', $subject_id))
            ->firstOrFail();

        $subject = Subject::findOrFail($subject_id);
        $level = Level::whereKey($level_id)
            ->where('subject_id', $subject_id)
            ->firstOrFail();

        if (mb_strtolower($subject->name) === 'coran') {
            $vocalTestUrl = route('vocal-test.create', [$subject, $level, $class]);

            if (auth()->guest()) {
                session()->put('url.intended', $vocalTestUrl);

                return redirect()->route('register')
                    ->with('info', 'Créez votre compte pour passer le test vocal du Coran.');
            }

            return redirect()->to($vocalTestUrl);
        }

        if (auth()->guest()) {
            return redirect()->route('register')
                ->with('info', 'Créez votre compte pour poursuivre votre inscription.');
        }

        return redirect()->route('appointment.create', ['type' => 'test'])
            ->with('info', 'Prenez rendez-vous pour votre test de niveau.');
    }

    /**
     * Affiche les classes d'un niveau (navigation publique)
     */
    public function publicClasses(Level $level)
    {
        $classes = \App\Models\ClassRoom::where('level_id', $level->id)
            ->withCount('subjects')
            ->get();

        return view('front.public-classes', compact('level', 'classes'));
    }

    /**
     * Affiche les matières d'une classe (navigation publique)
     */
    public function publicSubjects(Level $level, \App\Models\ClassRoom $class_room)
    {
        $class = $class_room;
        $subjects = Subject::whereIn('name', ['Arabe', 'Coran'])
            ->whereHas('classes', fn($q) => $q->where('class_room_id', $class->id))
            ->withCount('courses')
            ->get();

        return view('front.public-subjects', compact('level', 'class', 'subjects'));
    }

    /**
     * Affiche les cours d'une matière dans une classe (navigation publique)
     */
    public function publicCourses(Level $level, \App\Models\ClassRoom $class_room, Subject $subject)
    {
        $class = $class_room;
        $courses = Course::where('subject_id', $subject->id)
            ->where('class_id', $class->id)
            ->where('level_id', $level->id)
            ->withCount('learningTests')
            ->get();

        return view('front.public-courses', compact('level', 'class', 'subject', 'courses'));
    }

    public function religieux()
    {
        $subjects = \App\Models\Subject::withCount(['courses', 'classes'])
            ->where('type', 'religieux')
            ->where('name', 'Coran')
            ->get();

        $subjects->each(function ($subject) {
            if ($subject->courses_count > 0) {
                $subject->status_label = 'Disponible';
                $subject->status_icon = 'bi-check-circle-fill';
                $subject->status_color = '#4ADE80';
                $subject->status_bg = 'rgba(34,197,94,0.15)';
                $subject->status_border = 'rgba(34,197,94,0.2)';
            } elseif ($subject->classes_count > 0) {
                $subject->status_label = 'En cours';
                $subject->status_icon = 'bi-hourglass-split';
                $subject->status_color = '#FB923C';
                $subject->status_bg = 'rgba(251,146,60,0.15)';
                $subject->status_border = 'rgba(251,146,60,0.2)';
            } else {
                $subject->status_label = 'Non disponible';
                $subject->status_icon = 'bi-x-circle-fill';
                $subject->status_color = '#FCA5A5';
                $subject->status_bg = 'rgba(239,68,68,0.15)';
                $subject->status_border = 'rgba(239,68,68,0.2)';
            }
        });

        return view('front.religieux', compact('subjects'));
    }

    public function scolaires()
    {
        $subjects = \App\Models\Subject::withCount(['courses', 'classes'])
            ->where('type', 'scolaire')
            ->where('name', 'Arabe')
            ->get();

        $subjects->each(function ($subject) {
            if ($subject->courses_count > 0) {
                $subject->status_label = 'Disponible';
                $subject->status_icon = 'bi-check-circle-fill';
                $subject->status_color = '#4ADE80';
                $subject->status_bg = 'rgba(34,197,94,0.15)';
                $subject->status_border = 'rgba(34,197,94,0.2)';
            } elseif ($subject->classes_count > 0) {
                $subject->status_label = 'En cours';
                $subject->status_icon = 'bi-hourglass-split';
                $subject->status_color = '#FB923C';
                $subject->status_bg = 'rgba(251,146,60,0.15)';
                $subject->status_border = 'rgba(251,146,60,0.2)';
            } else {
                $subject->status_label = 'Non disponible';
                $subject->status_icon = 'bi-x-circle-fill';
                $subject->status_color = '#FCA5A5';
                $subject->status_bg = 'rgba(239,68,68,0.15)';
                $subject->status_border = 'rgba(239,68,68,0.2)';
            }
        });

        return view('front.scolaires', compact('subjects'));
    }
}
