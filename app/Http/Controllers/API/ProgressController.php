<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserProgress;
use App\Models\Course;
use App\Models\CourseView;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    /**
     * Progression globale de l'utilisateur
     * GET /api/progress
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $totalCourses = Course::approved()->count();
        $completedCourses = UserProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->count();

        $recentProgress = UserProgress::where('user_id', $user->id)
            ->with('course')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($p) => [
                'course_id'   => $p->course_id,
                'course_title' => $p->course?->title,
                'completed'   => (bool) $p->completed,
                'score'       => $p->score,
                'updated_at'  => $p->updated_at,
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'total_courses'          => $totalCourses,
                'completed_courses'      => $completedCourses,
                'completion_percentage'  => $totalCourses > 0
                    ? round(($completedCourses / $totalCourses) * 100, 1)
                    : 0,
                'recent_progress'        => $recentProgress,
            ],
        ]);
    }

    /**
     * Progression par matière
     * GET /api/progress/by-subject
     */
    public function bySubject(Request $request)
    {
        $user = $request->user();

        $progressBySubject = \DB::table('courses')
            ->join('subjects', 'courses.subject_id', '=', 'subjects.id')
            ->leftJoin('user_progress', function ($join) use ($user) {
                $join->on('courses.id', '=', 'user_progress.course_id')
                     ->where('user_progress.user_id', '=', $user->id);
            })
            ->selectRaw('subjects.id, subjects.name, COUNT(courses.id) as total')
            ->selectRaw('SUM(CASE WHEN user_progress.completed = 1 THEN 1 ELSE 0 END) as completed')
            ->groupBy('subjects.id', 'subjects.name')
            ->get()
            ->map(fn($row) => [
                'subject_id'           => $row->id,
                'subject_name'         => $row->name,
                'total_courses'        => (int) $row->total,
                'completed_courses'    => (int) $row->completed,
                'completion_percentage' => $row->total > 0
                    ? round(($row->completed / $row->total) * 100, 1)
                    : 0,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $progressBySubject,
        ]);
    }

    /**
     * Marquer un cours comme complété
     * POST /api/progress/{course}
     */
    public function markComplete(Request $request, Course $course)
    {
        $progress = UserProgress::updateOrCreate(
            [
                'user_id'   => $request->user()->id,
                'course_id' => $course->id,
            ],
            [
                'completed' => true,
                'score'     => $request->input('score', 100),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Progression mise à jour.',
            'data'    => [
                'completed' => (bool) $progress->completed,
                'score'     => $progress->score,
            ],
        ]);
    }
}
