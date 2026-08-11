<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\LearningPathService;
use App\Services\ContentAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseResourceController extends Controller
{
    private LearningPathService $paths;
    private ContentAccessService $contentAccess;

    public function __construct(
        LearningPathService $paths,
        ContentAccessService $contentAccess
    ) {
        $this->paths = $paths;
        $this->contentAccess = $contentAccess;
    }

    public function show(Request $request, Course $course, string $type)
    {
        abort_unless(in_array($type, ['video', 'pdf', 'link'], true), 404);
        abort_unless($request->hasValidSignature(), 403, 'Lien expiré ou invalide.');
        abort_unless($this->paths->userCanAccessCourse($request->user(), $course), 403);

        if (
            $request->user()
            && $request->user()->isStudent()
        ) {
            $decision =
                $this->contentAccess
                    ->acquireCourse(
                        $request,
                        $course
                    );

            abort_unless(
                $decision['allowed'],
                423,
                $decision['message']
            );
        }

        if ($type === 'link') {
            abort_unless($course->course_link, 404);
            return redirect()->away($course->course_link);
        }

        if ($type === 'video' && $course->video_url) {
            return redirect()->away($course->video_url);
        }

        $path = $type === 'video' ? $course->video : $course->pdf;
        abort_unless($path, 404);

        foreach (['local', 'public'] as $diskName) {
            $disk = Storage::disk($diskName);
            if ($disk->exists($path)) {
                $mime = $disk->mimeType($path) ?: 'application/octet-stream';
                return response()->file($disk->path($path), [
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
                    'Cache-Control' => 'private, no-store, max-age=0',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }
        }

        abort(404);
    }
}
