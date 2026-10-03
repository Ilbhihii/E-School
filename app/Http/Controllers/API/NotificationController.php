<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected PushNotificationService $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Enregistrer un token de périphérique pour les notifications push
     * POST /api/notifications/register-token
     */
    public function registerToken(Request $request)
    {
        $validated = $request->validate([
            'token'    => 'required|string|max:4096',
            'platform' => 'nullable|string|in:android,ios,web',
        ]);

        $user = $request->user();

        $token = trim((string) $validated['token']);
        $tokenHash = DeviceToken::hashToken($token);

        /*
         * Un token FCM représente un appareil précis. S'il était
         * précédemment associé à un autre compte (ordinateur partagé,
         * déconnexion/reconnexion), on réaffecte la même ligne au compte
         * courant au lieu de conserver deux lignes avec le même hash.
         * Cela respecte l'index unique device_tokens_token_hash_unique.
         */
        $deviceToken = DeviceToken::query()->updateOrCreate(
            [
                'token_hash' => $tokenHash,
            ],
            [
                'user_id' => $user->id,
                'token' => $token,
                'platform' => $validated['platform'] ?? 'android',
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Token enregistré avec succès.',
            'data'    => [
                'id'       => $deviceToken->id,
                'platform' => $deviceToken->platform,
                'is_active' => $deviceToken->is_active,
            ],
        ]);
    }

    /**
     * Désenregistrer un token (déconnexion, désinstallation)
     * POST /api/notifications/unregister-token
     */
    public function unregisterToken(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|max:4096',
        ]);

        $user = $request->user();

        $deleted = DeviceToken::query()
            ->where('user_id', $user->id)
            ->where(
                'token_hash',
                DeviceToken::hashToken((string) $validated['token'])
            )
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted > 0
                ? 'Token désenregistré avec succès.'
                : 'Token non trouvé.',
        ]);
    }

    /**
     * Désenregistrer TOUS les tokens de l'utilisateur (déconnexion totale)
     * POST /api/notifications/unregister-all
     */
    public function unregisterAll(Request $request)
    {
        $user = $request->user();

        $count = DeviceToken::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} token(s) désenregistré(s).",
        ]);
    }

    /**
     * Lister les tokens de l'utilisateur connecté
     * GET /api/notifications/tokens
     */
    public function tokens(Request $request)
    {
        $user = $request->user();

        $tokens = DeviceToken::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'platform', 'is_active', 'created_at']);

        return response()->json([
            'success' => true,
            'data'    => $tokens,
        ]);
    }

    /**
     * Vérifier l'état de la configuration Firebase
     * GET /api/notifications/status
     */
    public function status(Request $request)
    {
        $configured = $this->pushService->isConfigured();
        $user = $request->user();
        $activeTokens = DeviceToken::where('user_id', $user->id)->active()->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'firebase_configured' => $configured,
                'active_tokens'       => $activeTokens,
                'push_enabled'        => $configured && $activeTokens > 0,
            ],
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // PRÉFÉRENCES DE NOTIFICATION
    // ═══════════════════════════════════════════════════════════════

    /**
     * Obtenir les préférences de notification
     * GET /api/notifications/preferences
     */
    public function getPreferences(Request $request)
    {
        $user = $request->user();
        $preferences = $user->getOrCreateNotificationPreference();

        return response()->json([
            'success' => true,
            'data'    => $preferences,
        ]);
    }

    /**
     * Mettre à jour les préférences de notification
     * PUT /api/notifications/preferences
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'new_courses'          => 'sometimes|boolean',
            'live_reminders'       => 'sometimes|boolean',
            'appointment_updates'  => 'sometimes|boolean',
            'progress_updates'     => 'sometimes|boolean',
            'vocal_test_feedback'  => 'sometimes|boolean',
            'promotional'          => 'sometimes|boolean',
        ]);

        $user = $request->user();
        $preferences = $user->getOrCreateNotificationPreference();
        $preferences->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Préférences mises à jour avec succès.',
            'data'    => $preferences->fresh(),
        ]);
    }

    /**
     * Envoyer une notification de test
     * POST /api/notifications/test
     */
    public function sendTest(Request $request)
    {
        $user = $request->user();

        if (!$this->pushService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Firebase n\'est pas configuré sur le serveur.',
            ], 503);
        }

        $sent = $this->pushService->sendToUser(
            $user,
            '🔔 Test de notification',
            'Si vous lisez ceci, les notifications push fonctionnent !'
        );

        if ($sent > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Notification de test envoyée avec succès.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Aucun token actif trouvé pour cet utilisateur.',
        ], 404);
    }
}
