<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Course;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    /**
     * Détail d'une classe
     * GET /api/classes/{class}
     */
    public function show(ClassRoom $classRoom)
    {
        $classRoom->load(['level']);
        $classRoom->loadCount(['courses', 'subjects']);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'      => $classRoom->id,
                'name'    => $classRoom->name,
                'level'   => $classRoom->level ? [
                    'id'   => $classRoom->level->id,
                    'name' => $classRoom->level->name,
                ] : null,
                'courses_count'  => (int) $classRoom->courses_count,
                'subjects_count' => (int) $classRoom->subjects_count,
            ],
        ]);
    }

    /**
     * Cours d'une classe (filtrés par matière si spécifiée)
     * GET /api/classes/{class}/courses?subject_id=1
     */
    public function courses(ClassRoom $classRoom, Request $request)
    {
        $query = Course::where('class_id', $classRoom->id)
            ->with(['subject', 'level', 'module']);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }

        $courses = $query->orderBy('order')->orderBy('id')->get()
            ->map(fn($course) => [
                'id'            => $course->id,
                'title'         => $course->title,
                'description'   => $course->description,                'is_free'       => (bool) $course->is_free,
                'has_video'     => (bool) ($course->video || $course->video_url),
                'has_pdf'       => (bool) $course->pdf,
                'has_external_link' => (bool) $course->course_link,
                'order'         => $course->order,
                'subject'       => $course->subject ? ['id' => $course->subject->id, 'name' => $course->subject->name] : null,
                'level'         => $course->level ? ['id' => $course->level->id, 'name' => $course->level->name] : null,
                'has_test'      => $course->learningTests()->exists(),
                'created_at'    => $course->created_at,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $courses,
        ]);
    }

    /**
     * Matières d'une classe
     * GET /api/classes/{class}/subjects
     */
    public function subjects(ClassRoom $classRoom)
    {
        $subjects = $classRoom->subjects()
            ->withCount('courses')
            ->get()
            ->map(fn($subject) => [
                'id'            => $subject->id,
                'name'          => $subject->name,
                'type'          => $subject->type,
                'description'   => $subject->description,
                'courses_count' => (int) $subject->courses_count,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $subjects,
        ]);
    }
}
