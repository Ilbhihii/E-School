<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\ContentAccessService;
use App\Services\LearningPathService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentSessionController extends Controller
{
    public function heartbeat(
        Request $request,
        Course $course,
        LearningPathService $paths,
        ContentAccessService $contentAccess
    ): JsonResponse {
        abort_unless(
            $request->user()
            && $request->user()->isStudent(),
            403
        );

        abort_unless(
            $paths->userCanAccessCourse(
                $request->user(),
                $course
            ),
            403
        );

        $decision =
            $contentAccess
                ->heartbeatCourse(
                    $request,
                    $course
                );

        return response()->json(
            $decision,
            $decision['allowed']
                ? 200
                : 423
        );
    }

    public function release(
        Request $request,
        Course $course,
        ContentAccessService $contentAccess
    ): JsonResponse {
        abort_unless(
            $request->user()
            && $request->user()->isStudent(),
            403
        );

        $contentAccess
            ->releaseCourse(
                $request,
                $course
            );

        return response()->json([
            'released' => true,
        ]);
    }

    public function releaseDevice(
        Request $request,
        ContentAccessService $contentAccess
    ): JsonResponse {
        abort_unless(
            $request->user()
            && $request->user()->isStudent(),
            403
        );

        $contentAccess
            ->releaseCurrentDevice(
                $request
            );

        return response()->json([
            'released' => true,
        ]);
    }
}
