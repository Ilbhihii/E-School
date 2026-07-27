<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseTest;
use App\Models\UserProgress;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Liste des cours (public)
     * GET /api/courses?subject_id=1&level_id=1&class_id=1
     */
    public function index(Request $request)
    {
        $query = Course::with(['subject', 'level', 'classRoom']);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $courses = $query->orderBy('order')->orderBy('id')->get()
            ->map(fn($course) => $this->courseData($course));

        return response()->json([
            'success' => true,
            'data'    => $courses,
        ]);
    }

    /**
     * Détail d'un cours
     * GET /api/courses/{course}
     */
    public function show(Course $course)
    {
        $course->load(['subject', 'level', 'classRoom', 'learningTests']);

        $test = null;
        if ($course->learningTests->isNotEmpty()) {
            $test = $course->learningTests->first();
            $test->load('questions.answers');
        }

        $userProgress = null;
        if (auth()->check()) {
            $userProgress = UserProgress::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->first();
        }

        return response()->json([
            'success' => true,
            'data'    => array_merge($this->courseData($course), [
                'test'    => $test ? $this->testData($test) : null,
                'progress' => $userProgress ? [
                    'completed' => (bool) $userProgress->completed,
                    'score'     => $userProgress->score,
                ] : null,
            ]),
        ]);
    }

    /**
     * Marquer un cours comme terminé
     * POST /api/courses/{course}/complete
     */
    public function complete(Request $request, Course $course)
    {
        $user = $request->user();

        $progress = UserProgress::updateOrCreate(
            [
                'user_id'   => $user->id,
                'course_id' => $course->id,
            ],
            [
                'completed' => true,
                'score'     => $request->input('score', 100),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Cours marqué comme terminé.',
            'data'    => [
                'completed' => (bool) $progress->completed,
                'score'     => $progress->score,
            ],
        ]);
    }

    /**
     * Formater un cours
     */
    private function courseData(Course $course): array
    {
        return [
            'id'            => $course->id,
            'title'         => $course->title,
            'description'   => $course->description,
            'video_url'     => $course->video_url,
            'video'         => $course->video ? asset('storage/' . $course->video) : null,
            'pdf'           => $course->pdf ? asset('storage/' . $course->pdf) : null,
            'course_link'   => $course->course_link,
            'is_free'       => (bool) $course->is_free,
            'order'         => $course->order,
            'subject'       => $course->subject ? ['id' => $course->subject->id, 'name' => $course->subject->name] : null,
            'level'         => $course->level ? ['id' => $course->level->id, 'name' => $course->level->name] : null,
            'class'         => $course->classRoom ? ['id' => $course->classRoom->id, 'name' => $course->classRoom->name] : null,
            'created_at'    => $course->created_at,
        ];
    }

    /**
     * Formater un test
     */
    private function testData(CourseTest $test): array
    {
        return [
            'id'        => $test->id,
            'title'     => $test->title ?? 'Test de validation',
            'questions' => $test->questions->map(fn($q) => [
                'id'      => $q->id,
                'question' => $q->question_text ?? $q->question,
                'type'    => $q->type ?? 'multiple_choice',
                'answers' => $q->answers->map(fn($a) => [
                    'id'      => $a->id,
                    'answer'  => $a->answer_text ?? $a->answer,
                    'is_correct' => false, // Ne pas envoyer la bonne réponse au client
                ]),
            ]),
        ];
    }
}
