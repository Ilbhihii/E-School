<?php

// Import à ajouter en haut de routes/web.php :
use App\Http\Controllers\PublicScheduleController;

// Route publique à placer hors des groupes auth/admin/prof/student :
Route::get('/planning-des-classes', [PublicScheduleController::class, 'index'])
    ->name('public.schedule.index');
