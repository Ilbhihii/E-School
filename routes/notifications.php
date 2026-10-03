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

        Route::get('/push/config', [NotificationController::class, 'pushConfig'])
            ->middleware('throttle:30,1')
            ->name('push.config');

        Route::get('/push/status', [NotificationController::class, 'pushStatus'])
            ->middleware('throttle:30,1')
            ->name('push.status');

        Route::post('/push/register', [NotificationController::class, 'registerPushToken'])
            ->middleware('throttle:20,1')
            ->name('push.register');

        Route::post('/push/unregister', [NotificationController::class, 'unregisterPushToken'])
            ->middleware('throttle:20,1')
            ->name('push.unregister');

        Route::post('/push/test', [NotificationController::class, 'testPush'])
            ->middleware('throttle:5,1')
            ->name('push.test');

        Route::delete('/{notification}', [NotificationController::class, 'destroy'])
            ->name('destroy');
    });
