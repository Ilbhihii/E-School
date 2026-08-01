<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Subject;
use App\Services\ClassScheduleDisplayService;
use App\Services\LearningPathService;
use Illuminate\Http\Request;

class StudentScheduleController extends Controller
{
    public function index(
        Request $request,
        ClassScheduleDisplayService $scheduleService,
        LearningPathService $learningPathService
    ) {
        $student = $request->user();

        /*
         * L'étudiant ne peut filtrer que les parcours qui lui sont
         * réellement affectés dans class_user : matière → niveau → classe.
         */
        $assignmentRows = $learningPathService
            ->studentAssignmentRows((int) $student->id);

        $subjects = Subject::query()
            ->whereIn(
                'id',
                $assignmentRows->pluck('subject_id')->unique()
            )
            ->orderBy('name')
            ->get();

        $levels = Level::query()
            ->whereIn(
                'id',
                $assignmentRows->pluck('level_id')->unique()
            )
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $selectedSubjectId = $request->filled('subject_id')
            ? (int) $request->query('subject_id')
            : null;

        if (
            $selectedSubjectId
            && !$assignmentRows->contains(
                fn ($row) => (int) $row->subject_id === $selectedSubjectId
            )
        ) {
            $selectedSubjectId = null;
        }

        $selectedLevelId = $request->filled('level_id')
            ? (int) $request->query('level_id')
            : null;

        if (
            !$selectedSubjectId
            || (
                $selectedLevelId
                && !$assignmentRows->contains(
                    fn ($row) =>
                        (int) $row->subject_id === $selectedSubjectId
                        && (int) $row->level_id === $selectedLevelId
                )
            )
        ) {
            $selectedLevelId = null;
        }

        $levelsBySubject = $subjects
            ->mapWithKeys(function (Subject $subject) use ($assignmentRows, $levels) {
                $levelIds = $assignmentRows
                    ->where('subject_id', $subject->id)
                    ->pluck('level_id')
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values();

                $options = $levels
                    ->whereIn('id', $levelIds)
                    ->values()
                    ->map(fn (Level $level) => [
                        'id' => (int) $level->id,
                        'name' => $level->name,
                    ])
                    ->all();

                return [(string) $subject->id => $options];
            })
            ->all();

        $filters = array_filter([
            'subject_id' => $selectedSubjectId,
            'level_id' => $selectedLevelId,
        ]);

        $occurrences = $scheduleService->forStudent(
            $student,
            now(),
            35,
            null,
            $filters
        );

        $days = $occurrences->groupBy('date_key');

        $visibleAssignmentRows = $assignmentRows
            ->when(
                $selectedSubjectId,
                fn ($rows) => $rows->where('subject_id', $selectedSubjectId)
            )
            ->when(
                $selectedLevelId,
                fn ($rows) => $rows->where('level_id', $selectedLevelId)
            );

        $selectedSubject = $selectedSubjectId
            ? $subjects->firstWhere('id', $selectedSubjectId)
            : null;

        $selectedLevel = $selectedLevelId
            ? $levels->firstWhere('id', $selectedLevelId)
            : null;

        $availableClassCount = $visibleAssignmentRows
            ->pluck('class_id')
            ->unique()
            ->count();

        $hasActiveFilter = $selectedSubject !== null
            || $selectedLevel !== null;

        return view('student.schedule.index', compact(
            'occurrences',
            'days',
            'subjects',
            'levelsBySubject',
            'selectedSubjectId',
            'selectedLevelId',
            'selectedSubject',
            'selectedLevel',
            'availableClassCount',
            'hasActiveFilter'
        ));
    }
}
