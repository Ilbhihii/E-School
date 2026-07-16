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
use App\Http\Controllers\Student\TestController as StudentTestController;
use App\Http\Controllers\Prof\ProfController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Prof\ScheduleController;

use App\Http\Controllers\Prof\DevoirController as ProfDevoirController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\Admin\DevoirController;
use App\Http\Controllers\Front\LearningController;




use App\Http\Controllers\AppointmentController;
/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class,'index'])->name('home');

// Rendez-vous pour test
Route::get('/rendez-vous', [AppointmentController::class, 'create'])->name('appointment.create');
Route::post('/rendez-vous', [AppointmentController::class, 'store'])->name('appointment.store');

Route::get('/classes', [\App\Http\Controllers\Front\HomeController::class, 'classes'])->name('front.classes');
Route::get('/niveaux', [\App\Http\Controllers\Front\HomeController::class, 'niveaux'])->name('front.niveaux');

Route::get('/matieres/{id}/classes', [FrontController::class, 'subjectClasses'])
    ->name('front.subject.classes');

Route::get('/matieres/{id}/levels', [FrontController::class, 'subjectLevels'])
    ->name('front.subject.levels');

Route::get('/matieres/{subject}/levels/{level}/classes', [FrontController::class, 'levelClasses'])
    ->name('front.subject.level.classes');

Route::get('/matieres/{subject}/levels/{level}/classes/{class}/courses', [FrontController::class, 'courses'])
    ->name('front.courses');

Route::get('/levels/{id}/courses', [FrontController::class, 'levelCourses'])
    ->name('front.level.courses');

Route::get('/course/{id}', [FrontController::class, 'showCourse'])
    ->name('front.course.show');

// Navigation publique : Niveaux → Classes → Matières → Cours
Route::get('/classes/{level}/classes', [FrontController::class, 'publicClasses'])
    ->name('front.public.classes');
Route::get('/classes/{level}/classes/{class_room}/subjects', [FrontController::class, 'publicSubjects'])
    ->name('front.public.subjects');
Route::get('/classes/{level}/classes/{class_room}/subjects/{subject}/courses', [FrontController::class, 'publicCourses'])
    ->name('front.public.courses');

Route::get('/religieux', [FrontController::class, 'religieux'])
    ->name('front.religieux');

Route::get('/scolaires', [FrontController::class, 'scolaires'])
    ->name('front.scolaires');

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

    // AJAX : récupérer les matières d'une classe
    Route::get('/get-class-subjects/{classId}', [CourseController::class, 'getClassSubjects'])->name('get-class-subjects');

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

    // Navigation hiérarchique : Matières → Niveaux → Classes → Cours
    Route::get('/subjects', [LevelController::class, 'subjectsIndex'])->name('subjects.index');
    Route::get('/subjects/{subject}/levels', [LevelController::class, 'subjectLevels'])->name('subjects.levels');
    Route::get('/subjects/{subject}/levels/{level}/classes', [LevelController::class, 'subjectClasses'])->name('subjects.classes');

    // Ancienne navigation Niveaux → Classes (conservée pour la rétrocompatibilité)
    Route::get('/levels/{level}/classes', [LevelController::class, 'classes'])->name('levels.classes');
    Route::get('/levels/{level}/classes/{class}/subjects', [LevelController::class, 'subjects'])->name('levels.subjects');
    Route::get('/levels/{level}/classes/{class}/subjects/{subject}/courses', [LevelController::class, 'courses'])->name('levels.courses');
    Route::post('/levels/{level}/classes/{class}/subjects/attach', [LevelController::class, 'attachSubject'])->name('levels.subjects.attach');
    Route::delete('/levels/{level}/classes/{class}/subjects/{subject}/detach', [LevelController::class, 'detachSubject'])->name('levels.subjects.detach');
    Route::resource('levels', LevelController::class)->except(['create', 'edit', 'show']);

    Route::resource('users', UserController::class);
    Route::get('users/without-class', [UserController::class, 'withoutClass'])->name('users.without-class');
    Route::get('users/{user}/test-results', [UserController::class, 'testResults'])->name('users.test-results');
    Route::get('users/{userId}/test/{testId}/result', [UserController::class, 'showResult'])->name('users.test-result');
    Route::put('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::put('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

    // Navigation hiérarchique : Matières → Niveaux → Classes → Lives
    Route::get('/lives/subjects/{subject}/levels/{level}/classes/{class}', [LiveController::class, 'classLives'])->name('lives.class-lives');
    Route::get('/lives/subjects/{subject}/levels/{level}/classes', [LiveController::class, 'subjectClasses'])->name('lives.subject-classes');
    Route::get('/lives/subjects/{subject}/levels', [LiveController::class, 'subjectLevels'])->name('lives.subject-levels');

    Route::get('/lives', [LiveController::class, 'index'])->name('lives.index');
    Route::get('/lives/create', [LiveController::class, 'create'])->name('lives.create');
    Route::post('/lives', [LiveController::class, 'store'])->name('lives.store');
    Route::get('/lives/{live}/edit', [LiveController::class, 'edit'])->name('lives.edit');
    Route::put('/lives/{live}', [LiveController::class, 'update'])->name('lives.update');
    Route::delete('/lives/{live}', [LiveController::class, 'destroy'])->name('lives.destroy');
    
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

    // Assignation des professeurs : niveau + classe + matière
    Route::get('/prof-assignments', [UserController::class, 'profAssignments'])->name('users.prof-assignments');
    Route::post('/prof-assignments', [UserController::class, 'storeProfAssignment'])->name('users.store-prof-assignment');
    Route::delete('/prof-assignments/{id}', [UserController::class, 'destroyProfAssignment'])->name('users.destroy-prof-assignment');

    // Rendez-vous
    Route::get('/appointments', [\App\Http\Controllers\AppointmentController::class, 'index'])->name('appointments.index');
    Route::patch('/appointments/{appointment}/confirm', [\App\Http\Controllers\AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::patch('/appointments/{appointment}/cancel', [\App\Http\Controllers\AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::delete('/appointments/{appointment}', [\App\Http\Controllers\AppointmentController::class, 'destroy'])->name('appointments.destroy');

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

    // Navigation hiérarchique : Matières → Niveaux → Classes
    Route::get('/subjects', [ProfController::class, 'subjectsList'])->name('subjects.list');
    Route::get('/subjects/{subject}/levels', [ProfController::class, 'subjectLevels'])->name('subjects.levels');
    Route::get('/subjects/{subject}/levels/{level}/classes', [ProfController::class, 'subjectClasses'])->name('subjects.classes');

    // Browse routes (cours, lives, devoirs) à partir des classes
    Route::get('/browse/{level}/{class}/courses/{subject}', [ProfLevelController::class, 'courses'])->name('browse.courses');
    Route::get('/browse/{level}/{class}/lives', [ProfController::class, 'browseLives'])->name('browse.lives');
    Route::get('/browse/{level}/{class}/devoirs/{subject}', [ProfController::class, 'browseDevoirs'])->name('browse.devoirs');

    Route::resource('devoir', ProfDevoirController::class)->except(['show'])->names([
                    'index' => 'devoir.index',
                    'create' => 'devoir.create',
                    'store' => 'devoir.store',
                    'edit' => 'devoir.edit',
                    'update' => 'devoir.update',
                    'destroy' => 'devoir.destroy',
                ]);


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
// Routes étudiantes SANS vérification de paiement (waiting, tests, paiement)
Route::middleware(['auth'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

    Route::get('/levels', [StudentController::class, 'levels'])->name('levels');
    Route::get('/levels/{level}/classes', [StudentController::class, 'levelClasses'])->name('levels.classes');
    Route::get('/levels/{level}/classes/{class}/subjects', [StudentController::class, 'levelSubjects'])->name('levels.subjects');

    Route::get('/subjects', [StudentController::class, 'indexSubjects'])->name('subjects.index');
    Route::get('/subjects/{subject}/levels', [StudentController::class, 'subjectLevels'])->name('subjects.levels');
    Route::get('/subjects/{subject}/levels/{level}/classes', [StudentController::class, 'subjectClasses'])->name('subjects.classes');
    Route::get('/subjects/{subject}/levels/{level}/classes/{class}/courses', [StudentController::class, 'subjectCourses'])->name('subjects.courses');
    Route::get('/subjects/{level}', [StudentController::class, 'subjects'])->name('subjects');
    Route::get('/classes/{subject}/{level}', [StudentController::class, 'classes'])->name('classes');

    // Waiting
    Route::get('/waiting', [StudentController::class, 'waiting'])->name('waiting');

    // Tests
    Route::get('/tests', [StudentTestController::class, 'index'])->name('tests.index');
    Route::get('/tests/{test}', [StudentTestController::class, 'show'])->name('tests.show');
    Route::post('/tests/{test}', [StudentTestController::class, 'submit'])->name('tests.submit');

});

// Routes étudiantes protégées (nécessite un compte actif)
Route::middleware(['auth', 'active'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/lives', [StudentController::class, 'lives'])->name('lives');

    // Cours
    Route::get('/courses/{subject}/{class}', [StudentController::class, 'courses'])->name('courses');
    Route::get('/course/{id}', [StudentController::class, 'showCourse'])->name('course.show');

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

    // Profile & Settings
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::get('/settings', [StudentController::class, 'settings'])->name('settings');
    Route::put('/settings/profile', [StudentController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [StudentController::class, 'updatePassword'])->name('settings.password.update');

});

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
Route::post('/checkout', [PaymentController::class, 'checkout'])->name('student.checkout');

?>

