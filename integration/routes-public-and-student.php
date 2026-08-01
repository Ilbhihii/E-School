<?php

use App\Http\Controllers\PublicScheduleController;
use App\Http\Controllers\Student\StudentScheduleController;

/*
|--------------------------------------------------------------------------
| 1) Route visiteur : à placer hors des groupes admin/prof/student.
|--------------------------------------------------------------------------
*/
Route::get('/planning-des-classes', [PublicScheduleController::class, 'index'])
    ->name('public.schedule.index');

/*
|--------------------------------------------------------------------------
| 2) Route étudiant : à placer DANS votre groupe étudiant existant
| qui possède déjà prefix('student') et name('student.').
|--------------------------------------------------------------------------
*/
Route::get('/planning', [StudentScheduleController::class, 'index'])
    ->name('schedule.index');
