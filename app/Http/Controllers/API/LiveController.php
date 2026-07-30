<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Live;
use App\Services\LiveAccessService;
use Illuminate\Http\Request;

class LiveController extends Controller
{
    /**
     * Métadonnées publiques. Aucun lien Meet/Teams n'est exposé.
     */
    public function index()
    {
        $lives = Live::with([
                'classRoom',
                'user',
            ])
            ->latest()
            ->get()
            ->map(
                fn (Live $live) =>
                    $this->liveData($live)
            );

        return response()->json([
            'success' => true,
            'data' => $lives,
        ]);
    }

    /**
     * Lives à venir ou actuellement en cours.
     * Aucun lien externe n'est exposé.
     */
    public function upcoming()
    {
        $lives = Live::with([
                'classRoom',
                'user',
            ])
            ->orderBy('live_date')
            ->orderBy('start_time')
            ->get()
            ->filter(
                fn (Live $live) =>
                    in_array(
                        $live->schedule_status,
                        ['live', 'upcoming'],
                        true
                    )
            )
            ->values()
            ->map(
                fn (Live $live) =>
                    $this->liveData($live)
            );

        return response()->json([
            'success' => true,
            'data' => $lives,
        ]);
    }

    /**
     * Lives visibles par l'utilisateur connecté.
     */
    public function userLives(
        Request $request,
        LiveAccessService $accessService
    ) {
        $user = $request->user();

        $query = Live::with([
            'classRoom',
            'user',
        ]);

        if ($user->isStudent()) {
            $classIds = collect([
                $user->class_id,
            ])->filter();

            $assignedClassIds = \DB::table(
                'class_user'
            )
                ->where('user_id', $user->id)
                ->whereNotNull('class_id')
                ->pluck('class_id');

            $classIds = $classIds
                ->merge($assignedClassIds)
                ->unique()
                ->values();

            $query->whereIn(
                'class_id',
                $classIds
            );
        } elseif ($user->isProf()) {
            $query->where(function ($builder) use ($user) {
                $builder
                    ->where('user_id', $user->id)
                    ->orWhere('admin_id', $user->id);
            });
        }

        $lives = $query
            ->latest()
            ->get()
            ->map(function (Live $live) use (
                $user,
                $accessService
            ) {
                $decision = $accessService->evaluate(
                    $user,
                    $live
                );

                return $this->liveData(
                    $live,
                    $decision
                );
            });

        return response()->json([
            'success' => true,
            'data' => $lives,
        ]);
    }

    /**
     * Retourne le lien externe uniquement après toutes les vérifications.
     */
    public function join(
        Request $request,
        Live $live,
        LiveAccessService $accessService
    ) {
        $decision = $accessService->evaluate(
            $request->user(),
            $live
        );

        $accessService->record(
            $request,
            $live,
            $decision
        );

        if (!$decision['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $decision['message'],
                'reason' => $decision['code'],
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'stream_url' => $live->stream_url,
                'provider' => $live->provider,
            ],
        ]);
    }

    private function liveData(
        Live $live,
        ?array $decision = null
    ): array {
        return [
            'id' => $live->id,
            'title' => $live->title,
            'provider' => $live->provider,
            'live_date' => optional(
                $live->start_date_time
            )->toIso8601String(),
            'start_time' => optional(
                $live->start_date_time
            )->format('H:i'),
            'end_time' => optional(
                $live->end_date_time
            )->format('H:i'),
            'status' => $live->schedule_status,
            'status_label' => $live->status_label,
            'class' => $live->classRoom
                ? [
                    'id' => $live->classRoom->id,
                    'name' => $live->classRoom->name,
                ]
                : null,
            'user' => $live->user
                ? [
                    'id' => $live->user->id,
                    'name' => $live->user->name,
                ]
                : null,
            'can_join' => $decision['allowed']
                ?? false,
            'access_message' => $decision['message']
                ?? null,
            'join_endpoint' => $decision
                ? url(
                    '/api/user/lives/'
                    . $live->id
                    . '/join'
                )
                : null,
            'created_at' => $live->created_at,
        ];
    }
}
