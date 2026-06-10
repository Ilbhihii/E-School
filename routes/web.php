<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\LiveController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminScheduleController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Front\HomeController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Prof\ProfController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Prof\ScheduleController;
use App\Http\Controllers\Prof\TestController as ProfTestController;
use App\Http\Controllers\Prof\LiveController as ProfLiveController;
use App\Http\Controllers\Prof\DevoirController as ProfDevoirController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\Admin\DevoirController;
use App\Http\Controllers\Front\LearningController;




/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class,'index'])->name('home');

Route::get('/classes', [\App\Http\Controllers\Front\HomeController::class, 'classes'])->name('front.classes');

Route::get('/matieres/{id}/classes', [FrontController::class, 'subjectClasses'])
    ->name('front.subject.classes');

Route::get('/matieres/{subject_id}/classes/{class_id}/courses', [FrontController::class, 'courses'])
    ->name('front.courses');

Route::get('/matieres/{id}/levels', [FrontController::class, 'subjectLevels'])
    ->name('front.subject.levels');

Route::get('/levels/{id}/courses', [FrontController::class, 'levelCourses'])
    ->name('front.level.courses');

Route::get('/course/{id}', [FrontController::class, 'showCourse'])
    ->name('front.course.show');

Route::get('/religieux', [FrontController::class, 'religieux'])
    ->name('front.religieux');

Route::get('/all-classes-courses', [HomeController::class,'allClassesCourses'])->name('front.all-classes-courses');
Route::get('/lives', [HomeController::class,'lives'])->name('front.lives');

Route::get('/account/blocked', function () {
    return view('auth.account-blocked');
})->name('account.blocked');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| LEARNING (NIVEAUX / COURS / TESTS)
|--------------------------------------------------------------------------
*/

// PUBLIC (visiteur)
Route::get('/levels', [LearningController::class, 'levels'])->name('levels');

Route::get('/levels/{level}', [LearningController::class, 'courses'])
    ->name('levels.courses');

// Route moved to Public section: Route::get('/course/{id}', [FrontController::class, 'showCourse'])->name('front.course.show');

// PROTECTED (login obligatoire)
Route::middleware('auth')->group(function () {
    Route::post('/course/{id}/test', [LearningController::class, 'submitTest'])
        ->name('course.test');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
/*
|---------------------------
| COURSES (ADMIN + PROF)
|---------------------------
*/
Route::middleware(['auth','adminOrProf'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function(){

    Route::resource('courses', CourseController::class);

    

});

/*
|---------------------------
| ADMIN ONLY
|---------------------------
*/
Route::middleware(['auth','isAdmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function(){

    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    Route::resource('devoirs', DevoirController::class);

    Route::resource('classes', ClassController::class);
    Route::resource('levels', LevelController::class);

    Route::resource('users', UserController::class);
    Route::get('users/without-class', [UserController::class, 'withoutClass'])->name('users.without-class');
    Route::get('users/{user}/test-results', [UserController::class, 'testResults'])->name('users.test-results');
    Route::get('users/{userId}/test/{testId}/result', [UserController::class, 'showResult'])->name('users.test-result');
    Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

    Route::resource('lives', LiveController::class);
    
    Route::get('/schedule', [AdminScheduleController::class, 'index'])->name('schedule.index');
    Route::post('/schedule', [AdminScheduleController::class, 'store'])->name('schedule.store');
    

    
    Route::get('/chat', [ChatController::class, 'adminIndex'])->name('chat.list');
    Route::get('/chat/{subject}', [ChatController::class, 'adminChat'])->name('chat');
    Route::post('/chat/send', [ChatController::class, 'adminSend'])->name('chat.send');
    Route::delete('/chat/delete', [ChatController::class, 'adminDelete'])->name('chat.delete');

    // Absences routes
    Route::get('/absences', [DashboardController::class, 'absences'])->name('absences');
    Route::get('/absences/create', [DashboardController::class, 'create'])->name('absences.create');
    Route::post('/absences', [DashboardController::class, 'store'])->name('absences.store');
    Route::get('/absences/{absence}', [DashboardController::class, 'show'])->name('absences.show');
    Route::get('/absences/{absence}/edit', [DashboardController::class, 'edit'])->name('absences.edit');
    Route::put('/absences/{absence}', [DashboardController::class, 'update'])->name('absences.update');
    Route::delete('/absences/{absence}', [DashboardController::class, 'destroy'])->name('absences.destroy');

    // Assign class routes
    Route::get('/assign-class', [UserController::class, 'assignClass'])->name('assign.class');
    Route::post('/assign-class', [UserController::class, 'storeAssignment'])->name('assign.class.store');
    Route::patch('/assign-class/{pivot}', [UserController::class, 'updateAssignment'])->name('assign.class.update');
    Route::delete('/assign-class/{pivot}', [UserController::class, 'destroyAssignment'])->name('assign.class.destroy');

    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('profile');

    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

    Route::put('/settings/profile', [DashboardController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [DashboardController::class, 'updatePassword'])->name('settings.password.update');
});


/*
|--------------------------------------------------------------------------
| PROF - UNIFIED GROUP
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','isProf'])
    ->prefix('prof')
    ->name('prof.')
    ->group(function(){

    Route::get('/dashboard', [ProfController::class,'dashboard'])->name('dashboard');

    Route::get('/courses', [ProfController::class, 'courses'])->name('courses.index');
    
    Route::resource('courses', ProfController::class)->except(['index', 'show']);

    Route::get('/profile', function () {
        return view('prof.profile');
    })->name('profile');

    Route::get('/settings', function () {
        return view('prof.settings');
    })->name('settings');

    Route::put('/settings/profile', [ProfController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [ProfController::class, 'updatePassword'])->name('settings.password.update');

    Route::get('/chat/subjects', [ChatController::class, 'profSubjects'])
        ->name('chat.subjects');

    Route::get('/chat/{subject}', [ChatController::class, 'profChat'])
        ->name('chat');

    Route::post('/chat/send', [ChatController::class,'send'])
        ->name('chat.send');

    Route::delete('/chat/delete', [ChatController::class, 'profDelete'])
        ->name('chat.delete');

    Route::get('/assignments', [ProfController::class,'assignments'])
        ->name('assignments');

    Route::post('/grade', [ProfController::class,'grade'])
        ->name('grade');

    Route::get('/absences', [ProfController::class, 'absences'])
        ->name('absences');

    Route::get('/class-students/{id}', [ProfController::class, 'getStudents'])
        ->name('class.students');

    Route::post('/absences/store', [ProfController::class, 'storeAbsence'])
        ->name('absences.store');

    Route::get('/absences/list', [ProfController::class, 'absencesList'])
        ->name('absences.list');

    Route::put('/absences/{id}', [ProfController::class, 'updateAbsence'])
        ->name('absences.update');
        
    Route::get('/lives', [ProfController::class, 'livesIndex'])->name('lives.index');
    Route::resource('lives', ProfLiveController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('devoir', ProfDevoirController::class)->except(['show'])->names([
                    'index' => 'devoir.index',
                    'create' => 'devoir.create',
                    'store' => 'devoir.store',
                    'edit' => 'devoir.edit',
                    'update' => 'devoir.update',
                    'destroy' => 'devoir.destroy',
                ]);

    Route::resource('tests', ProfTestController::class);
    Route::post('tests/{test}/generate-ai', [ProfTestController::class, 'generateAI'])
        ->name('tests.generate-ai');
    Route::get('tests/{test}/student/{user}', [ProfTestController::class, 'studentResult'])
        ->name('tests.result.details');

    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
    Route::get('/schedule/data', [ScheduleController::class, 'data'])->name('schedule.data');
    Route::get('/classes', [ScheduleController::class, 'classes'])->name('classes');
    Route::post('/schedule/update', [ScheduleController::class, 'update'])->name('schedule.update');
    Route::post('/schedule/store', [ScheduleController::class, 'store'])->name('schedule.store');
    Route::delete('/schedule/{id}', [ScheduleController::class, 'destroy'])->name('schedule.destroy');
});

/*
|--------------------------------------------------------------------------
| STUDENT (PROTECTED READ-ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

    Route::get('/classes', [StudentController::class, 'classes'])->name('classes');

    Route::get('/classes/{class}/subjects', [StudentController::class, 'subjects'])->name('subjects');

    Route::get('/classes/{class}/subjects/{subject}/courses', [StudentController::class, 'courses'])->name('courses');

    Route::get('/course/{course}', [StudentController::class, 'showCourse'])->name('course.show');

    // Profile
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    
    // Settings  
    Route::get('/settings', [StudentController::class, 'settings'])->name('settings');
    
    // Profile updates
    Route::put('/settings/profile', [StudentController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [StudentController::class, 'updatePassword'])->name('settings.password.update');
    
    // Dashboard
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    
    // Lives
    Route::get('/lives', [StudentController::class, 'lives'])->name('lives');
    
    // Chats
    Route::get('/chats', [ChatController::class, 'subjects'])->name('chats');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/{subject}', [ChatController::class, 'index'])->name('student.chat');
    Route::delete('/chat/delete', [ChatController::class, 'delete'])->name('chat.delete');
    
    // Assignments
    Route::get('/assignments', [StudentController::class, 'assignments'])->name('assignments');
    Route::post('/assignments/send', [StudentController::class, 'sendAssignment'])->name('assignments.send');
    
    // Absences
    Route::get('/absences', [StudentController::class, 'absences'])->name('absences');

});

Route::get('/video/{filename}', function ($filename) {
    // Sécurité : interdire les chemins relatifs
    $filename = basename($filename);
    $path = storage_path('app/public/videos/' . $filename);

    if (!file_exists($path)) {
        abort(404, 'Vidéo introuvable');
    }

    $size  = filesize($path);
    $start = 0;
    $end   = $size - 1;
    $status = 200;

    $headers = [
        'Content-Type'              => 'video/mp4',
        'Accept-Ranges'             => 'bytes',
        'Cache-Control'             => 'no-cache, no-store',
        'Content-Disposition'       => 'inline',
        'X-Content-Type-Options'    => 'nosniff',
    ];

    // Gestion du Range (seekable video)
    if (request()->hasHeader('Range')) {
        $range = request()->header('Range');
        preg_match('/bytes=(\d+)-(\d*)/', $range, $matches);

        $start  = intval($matches[1]);
        $end    = (isset($matches[2]) && $matches[2] !== '') ? intval($matches[2]) : $size - 1;
        $end    = min($end, $size - 1);
        $length = $end - $start + 1;
        $status = 206;

        $headers['Content-Range']  = "bytes {$start}-{$end}/{$size}";
        $headers['Content-Length'] = $length;
    } else {
        $headers['Content-Length'] = $size;
    }

    $capturedStart = $start;
    $capturedEnd   = $end;

    return response()->stream(function () use ($path, $capturedStart, $capturedEnd) {
        // Vider tous les buffers PHP avant de streamer
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $stream = fopen($path, 'rb');
        fseek($stream, $capturedStart);
        $remaining = $capturedEnd - $capturedStart + 1;

        while (!feof($stream) && $remaining > 0) {
            $chunkSize = min(1024 * 64, $remaining); // 64KB par chunk
            $data = fread($stream, $chunkSize);
            if ($data === false) break;
            echo $data;
            $remaining -= strlen($data);
            flush();
        }

        fclose($stream);
    }, $status, $headers);

})->name('video.stream');

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| PLANS / OFFRES
|--------------------------------------------------------------------------
*/
Route::get('/plans', [PlanController::class, 'index'])->name('plans');

Route::get('/paypal/checkout', [PaymentController::class, 'paypalCheckout'])->name('paypal.checkout');

Route::get('/payment', [PaymentController::class, 'index'])->name('student.payment');

?>