<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

Route::get('prof/tests/{test}/student/{user}', [TestController::class, 'studentResult'])
    ->name('prof.tests.result.details');

Route::post('prof/tests/{test}/results/{result}/accept', [TestController::class, 'accept'])->name('prof.tests.accept');
Route::post('prof/tests/{test}/results/{result}/refuse', [TestController::class, 'refuse'])->name('prof.tests.refuse');

