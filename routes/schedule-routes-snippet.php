<?php

/*
| À placer DANS le groupe existant :
| Route::middleware(['auth','isAdmin'])->prefix('admin')->name('admin.')->group(...)
|
| Supprimez l'ancien bloc /schedule avant d'ajouter celui-ci.
*/

Route::get('/schedule/events', [AdminScheduleController::class, 'events'])
    ->name('schedule.events');
Route::get('/schedule', [AdminScheduleController::class, 'index'])
    ->name('schedule.index');
Route::post('/schedule', [AdminScheduleController::class, 'store'])
    ->name('schedule.store');
Route::put('/schedule/{schedule}', [AdminScheduleController::class, 'update'])
    ->name('schedule.update');
Route::delete('/schedule/{schedule}', [AdminScheduleController::class, 'destroy'])
    ->name('schedule.destroy');
