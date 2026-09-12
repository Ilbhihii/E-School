<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserProgress;
use App\Services\LearningPathService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function __construct(private LearningPathService $paths) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $accessible = Course::approved()->get()->filter(
            fn (Course $course) => $this->paths->userCanAccessCourse($user, $course)
        );
        $courseIds = $accessible->pluck('id');
        $totalCourses = $courseIds->count();
        $completedCourses = UserProgress::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)->where('completed', true)->count();

        $recentProgress = UserProgress::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)->with('course')->latest()->take(10)->get()
            ->map(fn ($p) => [
                'course_id' => $p->course_id,
                'course_title' => $p->course?->title,
                'completed' => (bool) $p->completed,
                'score' => $p->score,
                'updated_at' => $p->updated_at,
            ]);

        return response()->json(['success' => true, 'data' => [
            'total_courses' => $totalCourses,
            'completed_courses' => $completedCourses,
            'completion_percentage' => $totalCourses > 0 ? round(($completedCourses / $totalCourses) * 100, 1) : 0,
            'recent_progress' => $recentProgress,
        ]]);
    }

    public function bySubject(Request $request)
    {
        $user = $request->user();
        $courses = Course::approved()->with('subject')->get()->filter(
            fn (Course $course) => $this->paths->userCanAccessCourse($user, $course)
        );
        $courseIds = $courses->pluck('id');
        $completed = UserProgress::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)->where('completed', true)->pluck('course_id')->flip();

        $data = $courses->groupBy('subject_id')->map(function ($rows) use ($completed) {
            $first = $rows->first();
            $done = $rows->filter(fn ($course) => $completed->has($course->id))->count();
            $total = $rows->count();
            return [
                'subject_id' => $first->subject_id,
                'subject_name' => $first->subject?->name,
                'total_courses' => $total,
                'completed_courses' => $done,
                'completion_percentage' => $total > 0 ? round(($done / $total) * 100, 1) : 0,
            ];
        })->values();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function markComplete(Request $request, Course $course)
    {
        if (!$this->paths->userCanAccessCourse($request->user(), $course)) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé à ce cours.'], 403);
        }

        $progress = UserProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'course_id' => $course->id],
            ['completed' => true]
        );

        return response()->json(['success' => true, 'message' => 'Progression mise à jour.', 'data' => [
            'completed' => (bool) $progress->completed,
            'score' => $progress->score,
        ]]);
    }
}
