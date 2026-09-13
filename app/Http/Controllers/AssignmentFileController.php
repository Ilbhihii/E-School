<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\User;
use App\Services\LearningPathService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class AssignmentFileController extends Controller
{
    public function __invoke(Request $request, Assignment $assignment, LearningPathService $paths)
    {
        $user = $request->user();
        abort_unless($user && $this->canView($user, $assignment, $paths), 403);
        abort_unless($assignment->file, 404);

        foreach (['local', 'public'] as $diskName) {
            $disk = Storage::disk($diskName);
            if ($disk->exists($assignment->file)) {
                return response()->file($disk->path($assignment->file), [
                    'Content-Type' => $disk->mimeType($assignment->file) ?: 'application/octet-stream',
                    'Content-Disposition' => 'inline; filename="' . basename($assignment->file) . '"',
                    'Cache-Control' => 'private, no-store, max-age=0',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }
        }
        abort(404);
    }

    public function attachment(
        Request $request,
        Assignment $assignment,
        int $index,
        LearningPathService $paths
    ) {
        $user = $request->user();

        abort_unless(
            $user
            && $this->canView(
                $user,
                $assignment,
                $paths
            ),
            403
        );

        $files =
            $assignment->extra_files ?? [];

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
                        'nosn',
                ]
            );
        }

        abort(404);
    }
    private function canView(User $user, Assignment $assignment, LearningPathService $paths): bool
    {
        if ($user->isAdmin() || (int) $assignment->user_id === (int) $user->id) return true;

        $owner = $assignment->user;
        if (!$owner) return false;

        if ($user->isProf()) {
            if (!$owner->isStudent()) return false;
            return $paths->professorCanAccessStudent(
                $user,
                (int) $owner->id,
                (int) $assignment->subject_id,
                (int) ($assignment->classRoom?->level_id ?? 0),
                (int) $assignment->class_room_id
            );
        }

        if ($user->isStudent()) {
            // Devoir publié par un professeur/admin : vérifier compte, paiement et parcours.
            if ($owner->isProf() || $owner->isAdmin()) {
                if (!(bool) $user->is_active || !$user->hasCurrentPaidAccess()) {
                    return false;
                }
                if ($assignment->course_id && $assignment->course) {
                    return $paths->studentCanAccessCourse($user, $assignment->course);
                }

                $query = DB::table('class_user')
                    ->where('user_id', $user->id)
                    ->where('class_id', $assignment->class_room_id)
                    ->where('subject_id', $assignment->subject_id);

                if ($assignment->class_slot_id && Schema::hasColumn('class_user', 'class_slot_id')) {
                    $query->where('class_slot_id', $assignment->class_slot_id);
                }
                return $query->exists();
            }
            return false;
        }

        if ($user->isParent()) {
            return $user->children()
                ->where('users.id', $assignment->user_id)
                ->wherePivot('can_view_assignments', true)
                ->exists();
        }

        return false;
    }
}
