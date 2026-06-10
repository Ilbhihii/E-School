<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\Course;
use App\Models\CourseTest;
use App\Models\UserProgress;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
    // 1. Afficher niveaux
    public function levels()
    {
        $levels = Level::all();
        return view('front.levels', compact('levels'));
    }

    // 2. Cours par niveau
    public function courses($levelId)
    {
        $courses = Course::where('level_id', $levelId)
                        ->orderBy('order')
                        ->get();

        return view('front.courses', compact('courses'));
    }

    // 3. Voir cours
    public function showCourse($id)
    {
        $course = Course::with('learningTests')->findOrFail($id);

        return view('front.course-show', compact('course'));
    }

    // 4. Soumettre test
    public function submitTest(Request $request, $id)
    {
        $course = Course::with('learningTests')->findOrFail($id);

        $score = 0;
        $total = count($course->learningTests);

        foreach ($course->learningTests as $test) {
            if ($request->input('question_'.$test->id) == $test->correct_answer) {
                $score++;
            }
        }

        $percentage = ($score / $total) * 100;

        // Sauvegarde progression
        UserProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'course_id' => $course->id
            ],
            [
                'completed' => $percentage >= 60,
                'score' => $percentage
            ]
        );

        if ($percentage >= 60) {
            return back()->with('success', '✅ Test réussi ! Cours débloqué');
        } else {
            return back()->with('error', '❌ Test échoué. Réessayez.');
        }
    }

    // 5. Générateur test IA (simple)
    public function generateTest($courseId)
    {
        $course = Course::find($courseId);

        CourseTest::create([
            'course_id' => $course->id,
            'question' => 'Quelle est la définition principale ?',
            'option_a' => 'Réponse A',
            'option_b' => 'Réponse B',
            'option_c' => 'Réponse C',
            'correct_answer' => 'a'
        ]);

        return back();
    }
}

