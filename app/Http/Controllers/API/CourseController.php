<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserProgress;
use App\Services\LearningPathService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(private LearningPathService $paths) {}

    public function index(Request $request)
    {
        $query = Course::approved()->with(['subject', 'level', 'classRoom']);
        foreach (['subject_id', 'level_id', 'class_id'] as $field) {
            if ($request->filled($field)) $query->where($field, $request->input($field));
        }
        $courses = $query->orderBy('order')->orderBy('id')->get()->map(fn (Course $c) => $this->courseData($c));
        return response()->json(['success' => true, 'data' => $courses]);
    }

    public function show(Course $course)
    {
        $course->load(['subject', 'level', 'classRoom']);
        return response()->json(['success' => true, 'data' => $this->courseData($course)]);
    }

    public function authorizedShow(Request $request, Course $course)
    {
        $course->load(['subject', 'level', 'classRoom', 'learningTests']);
        if (!$this->paths->userCanAccessCourse($request->user(), $course)) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé à ce cours.'], 403);
        }
        $progress = UserProgress::where('user_id', $request->user()->id)->where('course_id', $course->id)->first();
        return response()->json(['success' => true, 'data' => array_merge($this->courseData($course), [
            'resource_endpoints' => [
                'video' => ($course->video || $course->video_url) ? route('api.courses.resource', [$course, 'video']) : null,
                'pdf' => $course->pdf ? route('api.courses.resource', [$course, 'pdf']) : null,
                'link' => $course->course_link ? route('api.courses.resource', [$course, 'link']) : null,
            ],
            'test' => $course->learningTests->isNotEmpty() ? [
                'title' => 'Test de validation',
                'questions' => $course->learningTests->map(fn ($question) => [
                    'id' => $question->id,
                    'question' => $question->question,
                    'type' => 'single_choice',
                    'answers' => collect([
                        ['id' => 1, 'answer' => $question->option_a],
                        ['id' => 2, 'answer' => $question->option_b],
                        ['id' => 3, 'answer' => $question->option_c],
                    ])->filter(fn ($a) => filled($a['answer']))->values(),
                ])->values(),
            ] : null,
            'progress' => $progress ? ['completed' => (bool) $progress->completed, 'score' => $progress->score] : null,
        ])]);
    }

    public function complete(Request $request, Course $course)
    {
        if (!$this->paths->userCanAccessCourse($request->user(), $course)) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé à ce cours.'], 403);
        }
        $progress = UserProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'course_id' => $course->id],
            ['completed' => true]
        );
        return response()->json(['success' => true, 'message' => 'Cours marqué comme terminé.', 'data' => [
            'completed' => (bool) $progress->completed, 'score' => $progress->score,
        ]]);
    }

    private function courseData(Course $course): array
    {
        return [
            'id' => $course->id, 'title' => $course->title, 'description' => $course->description,
            'is_free' => (bool) $course->is_free, 'order' => $course->order,
            'has_video' => (bool) ($course->video || $course->video_url), 'has_pdf' => (bool) $course->pdf,
            'has_external_link' => (bool) $course->course_link,
            'subject' => $course->subject ? ['id' => $course->subject->id, 'name' => $course->subject->name] : null,
            'level' => $course->level ? ['id' => $course->level->id, 'name' => $course->level->name] : null,
            'class' => $course->classRoom ? ['id' => $course->classRoom->id, 'name' => $course->classRoom->name] : null,
            'created_at' => $course->created_at,
        ];
    }
}
