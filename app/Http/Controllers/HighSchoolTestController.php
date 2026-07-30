<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\HighSchoolTestSubmission;
use App\Models\Level;
use App\Models\Subject;
use App\Models\VocalTestPrompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HighSchoolTestController extends Controller
{
    public function show(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        $test = $this->resolveTest(
            $subject,
            $level,
            $class
        );

        if (auth()->guest()) {
            session()->put(
                'url.intended',
                request()->fullUrl()
            );
        }

        $latestSubmission =
            auth()->check()
                ? $this->latestSubmission(
                    $subject,
                    $level,
                    $class
                )
                : null;

        if (
            $latestSubmission
            && $latestSubmission->isApproved()
            && auth()->user()->role === 'student'
        ) {
            return redirect()->route(
                'student.subjects.courses',
                [
                    $subject,
                    $level,
                    $class,
                ]
            )->with(
                'success',
                'Votre test est validé. '
                . 'Vous pouvez accéder aux cours.'
            );
        }

        if (
            $latestSubmission
            && $latestSubmission->isPendingReview()
            && auth()->user()->role === 'student'
        ) {
            return redirect()->route(
                'student.written-tests.show',
                $latestSubmission
            )->with(
                'info',
                'Votre test est déjà envoyé '
                . 'et attend sa correction.'
            );
        }

        return view(
            'front.high-school-test',
            compact(
                'subject',
                'level',
                'class',
                'test',
                'latestSubmission'
            )
        );
    }

    public function store(
        Request $request,
        Subject $subject,
        Level $level,
        ClassRoom $class
    ) {
        $test = $this->resolveTest(
            $subject,
            $level,
            $class
        );

        $latestSubmission =
            $this->latestSubmission(
                $subject,
                $level,
                $class
            );

        if (
            $latestSubmission
            && $latestSubmission->isApproved()
        ) {
            return redirect()->route(
                'student.subjects.courses',
                [
                    $subject,
                    $level,
                    $class,
                ]
            )->with(
                'success',
                'Votre test a déjà été validé.'
            );
        }

        if (
            $latestSubmission
            && $latestSubmission->isPendingReview()
        ) {
            return redirect()->route(
                'student.written-tests.show',
                $latestSubmission
            )->with(
                'info',
                'Votre précédent test attend encore '
                . 'sa correction.'
            );
        }

        $validated = $request->validate([
            'answer_images' => [
                'required',
                'array',
                'min:1',
                'max:5',
            ],
            'answer_images.*' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'confirmation' => [
                'accepted',
            ],
        ], [
            'answer_images.required' =>
                'Ajoutez au moins une image de vos réponses.',
            'answer_images.max' =>
                'Vous pouvez envoyer au maximum 5 images.',
            'answer_images.*.mimes' =>
                'Les réponses doivent être au format '
                . 'JPG, JPEG, PNG ou WEBP.',
            'answer_images.*.max' =>
                'Chaque image ne doit pas dépasser 5 Mo.',
            'confirmation.accepted' =>
                'Confirmez que les images sont lisibles.',
        ]);

        $existingSubmission =
            HighSchoolTestSubmission::query()
                ->where('user_id', auth()->id())
                ->where('subject_id', $subject->id)
                ->where('level_id', $level->id)
                ->where('class_id', $class->id)
                ->whereNull('consumed_at')
                ->latest()
                ->first();

        if ($existingSubmission) {
            return redirect()->route(
                'appointment.create',
                [
                    'type' => 'test',
                    'written_submission' =>
                        $existingSubmission->id,
                ]
            )->with(
                'info',
                'Une réponse est déjà prête. '
                . 'Terminez votre demande de rendez-vous.'
            );
        }

        $storedImages = [];

        try {
            foreach (
                $validated['answer_images']
                as $image
            ) {
                $path = $image->store(
                    'high-school-tests/'
                    . auth()->id(),
                    'local'
                );

                $storedImages[] = [
                    'path' => $path,
                    'original_name' =>
                        $image->getClientOriginalName(),
                    'mime_type' =>
                        $image->getMimeType(),
                    'size' =>
                        $image->getSize(),
                ];
            }

            $submission =
                HighSchoolTestSubmission::create([
                    'user_id' => auth()->id(),
                    'subject_id' => $subject->id,
                    'level_id' => $level->id,
                    'class_id' => $class->id,
                    'test_key' =>
                        $this->testKey($class),
                    'test_title' =>
                        $test['title'],
                    'questions_snapshot' =>
                        $test,
                    'answer_images' =>
                        $storedImages,
                    'status' =>
                        HighSchoolTestSubmission
                            ::STATUS_SUBMITTED,
                    'submitted_at' => now(),
                ]);
        } catch (\Throwable $exception) {
            foreach ($storedImages as $storedImage) {
                Storage::disk('local')->delete(
                    $storedImage['path']
                );
            }

            throw $exception;
        }

        return redirect()->route(
            'appointment.create',
            [
                'type' => 'test',
                'written_submission' =>
                    $submission->id,
            ]
        )->with(
            'success',
            'Vos réponses ont été importées. '
            . 'Complétez maintenant le rendez-vous.'
        );
    }

    public function image(
        HighSchoolTestSubmission $submission,
        int $index
    ) {
        $user = auth()->user();

        abort_unless(
            $user
            && (
                (int) $submission->user_id
                === (int) $user->id
                || in_array(
                    $user->role,
                    ['admin', 'prof'],
                    true
                )
            ),
            403
        );

        $image = $submission->images()[$index]
            ?? null;

        abort_unless(
            $image
            && !empty($image['path'])
            && Storage::disk('local')->exists(
                $image['path']
            ),
            404
        );

        return response()->file(
            Storage::disk('local')->path(
                $image['path']
            ),
            [
                'Content-Type' =>
                    $image['mime_type']
                    ?? 'image/jpeg',
                'Content-Disposition' =>
                    'inline; filename="reponse-test-'
                    . $submission->id
                    . '-'
                    . ($index + 1)
                    . '"',
                'Cache-Control' =>
                    'private, no-store',
            ]
        );
    }

    private function resolveTest(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ): array {
        abort_unless(
            VocalTestPrompt::normalizePathName(
                $subject->name
            ) === 'soutien lycee',
            404
        );

        abort_unless(
            (int) $level->subject_id
            === (int) $subject->id
            && VocalTestPrompt::normalizePathName(
                $level->name
            ) === 'bac',
            404
        );

        abort_unless(
            (int) $class->level_id
            === (int) $level->id
            && $class->subjects()
                ->where(
                    'subjects.id',
                    $subject->id
                )
                ->exists(),
            404
        );

        $test = config(
            'high_school_tests.'
            . $this->testKey($class)
        );

        abort_unless(
            is_array($test),
            404,
            'Aucun test écrit n’est configuré '
            . 'pour cette matière.'
        );

        return $test;
    }

    private function latestSubmission(
        Subject $subject,
        Level $level,
        ClassRoom $class
    ): ?HighSchoolTestSubmission {
        return HighSchoolTestSubmission::query()
            ->where(
                'user_id',
                auth()->id()
            )
            ->where(
                'subject_id',
                $subject->id
            )
            ->where(
                'level_id',
                $level->id
            )
            ->where(
                'class_id',
                $class->id
            )
            ->latest('submitted_at')
            ->latest('id')
            ->first();
    }

    private function testKey(
        ClassRoom $class
    ): string {
        return Str::slug(
            Str::ascii($class->name)
        );
    }
}
