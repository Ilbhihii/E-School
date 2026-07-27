<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SubjectController;
use App\Http\Controllers\API\LevelController;
use App\Http\Controllers\API\ClassRoomController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\LiveController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\VocalTestController;
use App\Http\Controllers\API\ProgressController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes — Smart School Academy (Flutter App)
|--------------------------------------------------------------------------
*/

/* ═══════════════════════════════════════════════════════════════
   ROUTES PUBLIQUES (sans authentification)
   ═══════════════════════════════════════════════════════════════ */

// ─── Authentification ───
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// ─── Sujets / Matières ───
Route::get('/subjects',               [SubjectController::class, 'index']);
Route::get('/subjects/{subject}',     [SubjectController::class, 'show']);
Route::get('/subjects/{subject}/levels', [SubjectController::class, 'levels']);

// ─── Niveaux ───
Route::get('/levels/{level}',                [LevelController::class, 'show']);
Route::get('/levels/{level}/classes',        [LevelController::class, 'classes']);

// ─── Classes ───
Route::get('/classes/{classRoom}',              [ClassRoomController::class, 'show']);
Route::get('/classes/{classRoom}/courses',      [ClassRoomController::class, 'courses']);
Route::get('/classes/{classRoom}/subjects',     [ClassRoomController::class, 'subjects']);

// ─── Cours ───
Route::get('/courses',              [CourseController::class, 'index']);
Route::get('/courses/{course}',     [CourseController::class, 'show']);

// ─── Lives ───
Route::get('/lives',            [LiveController::class, 'index']);
Route::get('/lives/upcoming',   [LiveController::class, 'upcoming']);

// ─── Rendez-vous ───
Route::get('/appointments/types', [AppointmentController::class, 'types']);

// ─── Test vocal ───
Route::get('/vocal-test/text', [VocalTestController::class, 'recitationText']);

// ─── Statistiques ───
Route::get('/home/stats', [HomeController::class, 'stats']);


/* ═══════════════════════════════════════════════════════════════
   ROUTES PROTÉGÉES (authentification Sanctum requise)
   ═══════════════════════════════════════════════════════════════ */

Route::middleware('auth:sanctum')->group(function () {

    // ─── Profil ───
    Route::post('/logout',              [AuthController::class, 'logout']);
    Route::get('/profile',              [AuthController::class, 'profile']);
    Route::put('/profile',              [AuthController::class, 'updateProfile']);
    Route::post('/profile/photo',       [AuthController::class, 'updateProfile']);

    // ─── Dashboard ───
    Route::get('/dashboard', [HomeController::class, 'dashboard']);

    // ─── Cours (protection pour marquer complété) ───
    Route::post('/courses/{course}/complete', [CourseController::class, 'complete']);

    // ─── Lives de l'utilisateur ───
    Route::get('/user/lives', [LiveController::class, 'userLives']);

    // ─── Rendez-vous ───
    Route::get('/appointments',             [AppointmentController::class, 'index']);
    Route::post('/appointments',            [AppointmentController::class, 'store']);

    // ─── Test vocal ───
    Route::post('/vocal-test/submit',       [VocalTestController::class, 'submit']);
    Route::get('/vocal-test/submissions',   [VocalTestController::class, 'submissions']);

    // ─── Progression ───
    Route::get('/progress',                 [ProgressController::class, 'index']);
    Route::get('/progress/by-subject',      [ProgressController::class, 'bySubject']);
    Route::post('/progress/{course}',       [ProgressController::class, 'markComplete']);

    // ─── Notifications Push (FCM) ───
    Route::prefix('notifications')->group(function () {
        Route::post('/register-token',      [NotificationController::class, 'registerToken']);
        Route::post('/unregister-token',    [NotificationController::class, 'unregisterToken']);
        Route::post('/unregister-all',      [NotificationController::class, 'unregisterAll']);
        Route::get('/tokens',               [NotificationController::class, 'tokens']);
        Route::get('/status',               [NotificationController::class, 'status']);
        Route::get('/preferences',          [NotificationController::class, 'getPreferences']);
        Route::put('/preferences',          [NotificationController::class, 'updatePreferences']);
        Route::post('/test',                [NotificationController::class, 'sendTest']);
    });
});
