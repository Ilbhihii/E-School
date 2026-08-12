<?php

namespace App\Http\Controllers\API;

use App\Services\ContentAccessService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur
     * POST /api/register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        // Toute inscription publique crée uniquement un étudiant.
        $validated['role'] = User::ROLE_STUDENT;
        $validated['is_active'] = false;

        $user = User::create($validated);

        $token = $user->createToken('flutter-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Compte créé avec succès. En attente d\'activation.',
            'data' => [
                'user'  => $this->userData($user),
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Connexion
     * POST /api/login
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte n\'a pas encore été activé par l\'administrateur.',
            ], 403);
        }

        // Révoquer les anciens tokens
        $user->tokens()->delete();

        $token = $user->createToken('flutter-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie.',
            'data' => [
                'user'  => $this->userData($user),
                'token' => $token,
            ],
        ]);
    }

    /**
     * Déconnexion
     * POST /api/logout
     */
    public function logout(
        Request $request,
        ContentAccessService $contentAccess
    )
    {
        if (
            $request->user()
            && $request->user()->isStudent()
        ) {
            $contentAccess->releaseCurrentDevice(
                $request
            );
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie.',
        ]);
    }

    /**
     * Profil de l'utilisateur connecté
     * GET /api/profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        // Charger les relations disponibles
        $user->loadMissing(['classRoom', 'results']);

        // Charger classes uniquement si la table existe
        try {
            $user->load('classes');
        } catch (\Exception $e) {
            // Table classe_user peut ne pas exister
        }

        return response()->json([
            'success' => true,
            'data'    => $this->userData($user),
        ]);
    }

    /**
     * Mise à jour du profil
     * PUT /api/profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'email'         => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password'      => 'sometimes|string|min:8|confirmed',
            'profile_photo' => 'sometimes|image|max:2048',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('profiles', 'public');
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès.',
            'data'    => $this->userData($user->fresh()),
        ]);
    }

    /**
     * Demander un lien de réinitialisation de mot de passe
     * POST /api/forgot-password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return response()->json([
            'success' => $status === Password::RESET_LINK_SENT,
            'message' => __($status),
        ]);
    }

    /**
     * Formater les données utilisateur pour l'API
     */
    private function userData(User $user): array
    {
        $classes = collect();
        try {
            $classes = $user->classes->map(fn($c) => ['id' => $c->id, 'name' => $c->name]);
        } catch (\Exception $e) {
            // Table classe_user peut ne pas être disponible
        }

        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'role'              => $user->role,
            'profile_photo'     => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
            'is_active'         => $user->is_active,
            'is_paid'           => $user->is_paid,
            'subscription_type' => $user->subscription_type,
            'test_passed'       => $user->test_passed,
            'class_id'          => $user->class_id,
            'class_name'        => $user->classRoom?->name,
            'classes'           => $classes,
            'created_at'        => $user->created_at,
        ];
    }
}
