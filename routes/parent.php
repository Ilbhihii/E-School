<?php

use App\Http\Controllers\Admin\ParentController as AdminParentController;
use App\Http\Controllers\Parent\ParentController;
use App\Http\Middleware\IsParent;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'isAdmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/parents', [AdminParentController::class, 'index'])->name('parents.index');
        Route::post('/parents', [AdminParentController::class, 'store'])->name('parents.store');
        Route::post('/parents/{parent}/children', [AdminParentController::class, 'linkChild'])->name('parents.children.store');
        Route::delete('/parents/{parent}/children/{student}', [AdminParentController::class, 'unlinkChild'])->name('parents.children.destroy');
        Route::delete('/parents/{parent}', [AdminParentController::class, 'destroy'])->name('parents.destroy');
    });

Route::middleware(['auth', IsParent::class])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {
        Route::get('/dashboard', [ParentController::class, 'dashboard'])->name('dashboard');
        Route::get('/children/{student}', [ParentController::class, 'show'])->name('children.show');
        Route::get('/children/{student}/schedule', [ParentController::class, 'schedule'])->name('children.schedule');
        Route::get('/children/{student}/absences', [ParentController::class, 'absences'])->name('children.absences');
        Route::get('/children/{student}/assignments', [ParentController::class, 'assignments'])->name('children.assignments');
        Route::get('/children/{student}/results', [ParentController::class, 'results'])->name('children.results');
    });
