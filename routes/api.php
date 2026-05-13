<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LevelController;
use App\Http\Controllers\API\CourseController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::get('/levels', [LevelController::class, 'index']);
Route::get('/courses/{subject}', [CourseController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/course', [CourseController::class, 'store']);
});



