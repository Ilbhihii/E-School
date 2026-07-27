<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Live;
use App\Models\Subject;
use App\Models\UserProgress;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Statistiques générales pour l'accueil
     * GET /api/home/stats
     */
    public function stats()
    {
        $totalClasses  = ClassRoom::count();
        $totalCourses  = Course::count();
        $totalSubjects = Subject::whereIn('name', ['Arabe', 'Coran'])->count();
        $upcomingLives = Live::whereDate('live_date', '>=', now())
            ->orderBy('live_date')
            ->take(3)
            ->get()
            ->map(fn($live) => [
                'id'         => $live->id,
                'title'      => $live->title,
                'live_date'  => $live->live_date,
                'start_time' => $live->start_time,
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'total_classes'  => $totalClasses,
                'total_courses'  => $totalCourses,
                'total_subjects' => $totalSubjects,
                'upcoming_lives' => $upcomingLives,
            ],
        ]);
    }

    /**
     * Tableau de bord de l'utilisateur connecté
     * GET /api/dashboard
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $totalCourses  = Course::count();
        $completedCourses = UserProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->count();

        $subjectNames = ['Arabe', 'Coran'];
        $availableSubjects = Subject::whereIn('name', $subjectNames)
            ->withCount('courses')
            ->get()
            ->map(fn($s) => [
                'id'            => $s->id,
                'name'          => $s->name,
                'type'          => $s->type,
                'courses_count' => (int) $s->courses_count,
            ]);

        $recentCourses = UserProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->with('course')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'id'    => $p->course?->id,
                'title' => $p->course?->title,
                'score' => $p->score,
            ]);

        $upcomingLives = Live::whereDate('live_date', '>=', now())
            ->orderBy('live_date')
            ->take(3)
            ->get()
            ->map(fn($live) => [
                'id'         => $live->id,
                'title'      => $live->title,
                'live_date'  => $live->live_date,
                'start_time' => $live->start_time,
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'role'  => $user->role,
                ],
                'stats' => [
                    'total_courses'         => $totalCourses,
                    'completed_courses'     => $completedCourses,
                    'completion_percentage' => $totalCourses > 0
                        ? round(($completedCourses / $totalCourses) * 100, 1)
                        : 0,
                ],
                'available_subjects' => $availableSubjects,
                'recent_courses'     => $recentCourses,
                'upcoming_lives'     => $upcomingLives,
            ],
        ]);
    }
}
