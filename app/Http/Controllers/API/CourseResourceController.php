<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\LearningPathService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseResourceController extends Controller
{
    private LearningPathService $paths;

    public function __construct(LearningPathService $paths)
    {
        $this->paths = $paths;
    }

    public function show(Request $request, Course $course, string $type)
    {
        if (!in_array($type, ['video', 'pdf', 'link'], true)) {
            return response()->json(['success' => false, 'message' => 'Ressource inconnue.'], 404);
        }

        if (!$this->paths->userCanAccessCourse($request->user(), $course)) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé à ce cours.'], 403);
        }

        if ($type === 'link') {
            return response()->json([
                'success' => true,
                'data' => ['url' => $course->course_link],
            ]);
        }

        if ($type === 'video' && $course->video_url) {
            return response()->json([
                'success' => true,
                'data' => ['url' => $course->video_url],
            ]);
        }

        $path = $type === 'video' ? $course->video : $course->pdf;
        if (!$path) {
            return response()->json(['success' => false, 'message' => 'Ressource indisponible.'], 404);
        }

        foreach (['local', 'public'] as $diskName) {
            $disk = Storage::disk($diskName);
            if ($disk->exists($path)) {
                return response()->file($disk->path($path), [
                    'Content-Type' => $disk->mimeType($path) ?: 'application/octet-stream',
                    'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
                    'Cache-Control' => 'private, no-store, max-age=0',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Fichier introuvable.'], 404);
    }
}
