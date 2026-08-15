<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function () {
        Route::get('/', [NotificationController::class, 'index'])
            ->name('index');

        Route::get('/feed', [NotificationController::class, 'feed'])
            ->name('feed');

        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])
            ->name('read-all');

        Route::delete('/read', [NotificationController::class, 'clearRead'])
            ->name('clear-read');

        Route::get('/{notification}/open', [NotificationController::class, 'open'])
            ->name('open');

        Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])
            ->name('read');

        Route::delete('/{notification}', [NotificationController::class, 'destroy'])
            ->name('destroy');
    });
