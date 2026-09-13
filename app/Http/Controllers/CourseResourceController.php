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

    public function attachment(
        Request $request,
        Course $course,
        int $index
    ) {
        $user = $request->user();

        abort_unless($user, 401);

        abort_unless(
            $this->paths->userCanAccessCourse(
                $user,
                $course
            ),
            403
        );

        if ($user->isStudent()) {
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

        $files =
            $course->extra_files ?? [];

        abort_unless(
            array_key_exists(
                $index,
                $files
            ),
            404
        );

        $file = $files[$index];
        $path = $file['path'] ?? null;

        abort_unless($path, 404);

        foreach (
            ['local', 'public']
            as $diskName
        ) {
            $disk =
                Storage::disk($diskName);

            if (!$disk->exists($path)) {
                continue;
            }

            return response()->download(
                $disk->path($path),
                $file['name']
                    ?? basename($path),
                [
                    'Content-Type' =>
                        $disk->mimeType($path)
                        ?: 'application/octet-stream',
                    'Cache-Control' =>
                        'private, no-store, max-age=0',
                    'X-Content-Type-Options' =>
                        'nosniff',
                ]
            );
        }

        abort(404);
    }

}
