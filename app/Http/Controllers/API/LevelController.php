<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    /**
     * Détail d'un niveau
     * GET /api/levels/{level}
     */
    public function show(Level $level)
    {
        $level->load(['subject']);
        $level->loadCount('classes');

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $level->id,
                'name'        => $level->name,
                'description' => $level->description,
                'order'       => $level->order,
                'subject'     => $level->subject ? [
                    'id'   => $level->subject->id,
                    'name' => $level->subject->name,
                ] : null,
                'classes_count' => (int) $level->classes_count,
            ],
        ]);
    }

    /**
     * Classes d'un niveau
     * GET /api/levels/{level}/classes
     */
    public function classes(Level $level)
    {
        $classes = ClassRoom::where('level_id', $level->id)
            ->with('subjects')
            ->withCount(['courses', 'subjects', 'students'])
            ->get()
            ->map(fn($class) => [
                'id'             => $class->id,
                'name'           => $class->name,
                'courses_count'  => (int) $class->courses_count,
                'subjects_count' => (int) $class->subjects_count,
                'students_count' => (int) $class->students_count,
                'subjects'       => $class->subjects->map(fn($s) => [
                    'id'   => $s->id,
                    'name' => $s->name,
                ]),
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'level'   => [
                    'id'          => $level->id,
                    'name'        => $level->name,
                    'description' => $level->description,
                ],
                'classes' => $classes,
            ],
        ]);
    }
}
