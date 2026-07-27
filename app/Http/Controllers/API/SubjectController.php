<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Level;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Liste de toutes les matières
     * GET /api/subjects
     */
    public function index()
    {
        $subjects = Subject::withCount(['courses', 'levels', 'classes'])
            ->whereIn('name', ['Arabe', 'Coran'])
            ->get()
            ->map(fn($subject) => $this->subjectData($subject));

        return response()->json([
            'success' => true,
            'data'    => $subjects,
        ]);
    }

    /**
     * Détail d'une matière
     * GET /api/subjects/{subject}
     */
    public function show(Subject $subject)
    {
        $subject->loadCount(['courses', 'levels', 'classes']);

        return response()->json([
            'success' => true,
            'data'    => $this->subjectData($subject),
        ]);
    }

    /**
     * Niveaux d'une matière
     * GET /api/subjects/{subject}/levels
     */
    public function levels(Subject $subject)
    {
        $levels = Level::where('subject_id', $subject->id)
            ->withCount([
                'courses' => fn($q) => $q->where('subject_id', $subject->id),
                'classes as classes_count' => fn($q) => $q
                    ->whereHas('subjects', fn($sq) => $sq->where('subjects.id', $subject->id)),
            ])
            ->orderBy('order')
            ->orderBy('id')
            ->get()
            ->map(fn($level) => [
                'id'            => $level->id,
                'name'          => $level->name,
                'description'   => $level->description,
                'order'         => $level->order,
                'courses_count' => (int) $level->courses_count,
                'classes_count' => (int) $level->classes_count,
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'subject' => $this->subjectData($subject),
                'levels'  => $levels,
            ],
        ]);
    }

    /**
     * Formater une matière
     */
    private function subjectData(Subject $subject): array
    {
        return [
            'id'            => $subject->id,
            'name'          => $subject->name,
            'type'          => $subject->type,
            'description'   => $subject->description,
            'image'         => $subject->image ? asset('storage/' . $subject->image) : null,
            'courses_count' => (int) $subject->courses_count,
            'levels_count'  => (int) $subject->levels_count,
            'classes_count' => (int) $subject->classes_count,
        ];
    }
}
