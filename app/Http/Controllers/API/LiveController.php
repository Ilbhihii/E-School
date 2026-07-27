<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Live;
use Illuminate\Http\Request;

class LiveController extends Controller
{
    /**
     * Liste de tous les lives (public)
     * GET /api/lives
     */
    public function index()
    {
        $lives = Live::with(['classRoom', 'user'])
            ->latest()
            ->get()
            ->map(fn($live) => $this->liveData($live));

        return response()->json([
            'success' => true,
            'data'    => $lives,
        ]);
    }

    /**
     * Lives à venir (public)
     * GET /api/lives/upcoming
     */
    public function upcoming()
    {
        $lives = Live::with(['classRoom', 'user'])
            ->whereDate('live_date', '>=', now())
            ->orderBy('live_date')
            ->orderBy('start_time')
            ->get()
            ->map(fn($live) => $this->liveData($live));

        return response()->json([
            'success' => true,
            'data'    => $lives,
        ]);
    }

    /**
     * Lives d'un utilisateur connecté
     * GET /api/user/lives
     */
    public function userLives(Request $request)
    {
        $user = $request->user();

        $lives = Live::with(['classRoom', 'user'])
            ->where(function ($q) use ($user) {
                // Lives de la classe de l'utilisateur
                if ($user->class_id) {
                    $q->where('class_id', $user->class_id);
                }
                // Lives créés par l'utilisateur (prof/admin uniquement)
                if ($user->isProf() || $user->isAdmin()) {
                    $q->orWhere('user_id', $user->id)
                      ->orWhere('admin_id', $user->id);
                }
            })
            ->latest()
            ->get()
            ->map(fn($live) => $this->liveData($live));

        return response()->json([
            'success' => true,
            'data'    => $lives,
        ]);
    }

    /**
     * Formater un live
     */
    private function liveData(Live $live): array
    {
        return [
            'id'           => $live->id,
            'title'        => $live->title,
            'stream_url'   => $live->stream_url,
            'provider'     => $live->provider,
            'live_date'    => $live->live_date,
            'start_time'   => $live->start_time,
            'end_time'     => $live->end_time,
            'class'        => $live->classRoom ? [
                'id'   => $live->classRoom->id,
                'name' => $live->classRoom->name,
            ] : null,
            'user'         => $live->user ? [
                'id'   => $live->user->id,
                'name' => $live->user->name,
            ] : null,
            'teams_app_url' => $live->teams_app_url,
            'created_at'   => $live->created_at,
        ];
    }
}
