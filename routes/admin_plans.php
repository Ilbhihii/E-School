<?php

use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GESTION ADMIN DES OFFRES
|--------------------------------------------------------------------------
|
| Ce fichier est chargé depuis routes/web.php avec :
| require __DIR__ . '/admin_plans.php';
|
| Il n'écrase aucune route existante du projet.
|
*/
Route::middleware(['auth', 'isAdmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('plans')
            ->name('plans.')
            ->group(function () {
                Route::get(
                    '/',
                    [AdminPlanController::class, 'index']
                )->name('index');

                Route::get(
                    '/create',
                    [AdminPlanController::class, 'create']
                )->name('create');

                Route::post(
                    '/',
                    [AdminPlanController::class, 'store']
                )->name('store');

                Route::get(
                    '/{plan}/edit',
                    [AdminPlanController::class, 'edit']
                )->name('edit');

                Route::put(
                    '/{plan}',
                    [AdminPlanController::class, 'update']
                )->name('update');

                Route::patch(
                    '/{plan}/status',
                    [AdminPlanController::class, 'toggleStatus']
                )->name('status');

                Route::delete(
                    '/{plan}',
                    [AdminPlanController::class, 'destroy']
                )->name('destroy');
            });
    });
