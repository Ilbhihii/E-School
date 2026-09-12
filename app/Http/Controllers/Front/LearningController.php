<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\Course;
use App\Models\CourseTest;
use App\Models\UserProgress;
use Illuminate\Support\Facades\Auth;
use App\Services\LearningPathService;

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
        $courses = Course::approved()->where('level_id', $levelId)
                        ->orderBy('order')
                        ->get();

        return view('front.courses', compact('courses'));
    }

    // 3. Voir cours
    public function showCourse($id)
    {
        $course = Course::approved()->with('learningTests')->findOrFail($id);

        return view('front.course-show', compact('course'));
    }

    // 4. Soumettre test
    public function submitTest(Request $request, $id, LearningPathService $paths)
    {
        $course = Course::approved()->with('learningTests')->findOrFail($id);

        abort_unless($paths->userCanAccessCourse($request->user(), $course), 403);

        $score = 0;
        $total = count($course->learningTests);

        if ($total === 0) {
            return back()->with('error', 'Aucune question de validation n’est disponible pour ce cours.');
        }

        foreach ($course->learningTests as $test) {
            $answer = $request->input('question_'.$test->id);
            if (is_string($answer) && in_array($answer, ['a', 'b', 'c'], true) && hash_equals((string) $test->correct_answer, $answer)) {
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
        $course = Course::approved()->find($courseId);

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

