<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\LiveController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProfessorController;
use App\Http\Controllers\Admin\ProfessorAvailabilityController;
use App\Http\Controllers\Admin\AdminScheduleController;
use App\Http\Controllers\Admin\HighSchoolTestReviewController;
use App\Http\Controllers\Admin\ContactLeadController;
use App\Http\Controllers\Admin\DevoirController;

use App\Http\Controllers\PublicScheduleController;

use App\Http\Controllers\Student\StudentScheduleController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\ContentSessionController;
use App\Http\Controllers\Student\TestController as StudentTestController;
use App\Http\Controllers\Student\HighSchoolTestHistoryController;

use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\AIChatbotController;
use App\Http\Controllers\Front\LearningController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;

use App\Http\Controllers\PlanController;
use App\Http\Controllers\ChatController;

use App\Http\Controllers\Prof\ProfController;
use App\Http\Controllers\Prof\FirstPasswordController;
use App\Http\Controllers\Prof\ProfLevelController;
use App\Http\Controllers\Prof\ScheduleController;
use App\Http\Controllers\Prof\DevoirController as ProfDevoirController;
use App\Http\Controllers\Prof\CourseController as ProfCourseController;

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\LiveAccessController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\VocalTestController;
use App\Http\Controllers\HighSchoolTestController;
use App\Http\Controllers\CourseResourceController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    [HomeController::class, 'index']
)->name('home');

/*
|--------------------------------------------------------------------------
| CHATBOT IA PUBLIC — VISITEURS UNIQUEMENT
|--------------------------------------------------------------------------
|
| Le composant Blade est protégé par @guest.
| Le contrôleur vérifie aussi Auth::check() et renvoie 403
| à tout utilisateur connecté (étudiant, prof ou admin).
|
*/
Route::post(
    '/assistant-ia/message',
    [AIChatbotController::class, 'chat']
)
    ->middleware('throttle:8,1')
    ->name('visitor.ai-chatbot.chat');

// Prise de contact depuis la page d'accueil
Route::post(
    '/prise-de-contact',
    [ContactController::class, 'store']
)
    ->middleware('throttle:5,1')
    ->name('contact.store');

// Planning public des classes
Route::get(
    '/planning-des-classes',
    [PublicScheduleController::class, 'index']
)->name('public.schedule.index');

// Rendez-vous pour test
Route::get(
    '/rendez-vous',
    [AppointmentController::class, 'create']
)->name('appointment.create');

Route::post(
    '/rendez-vous',
    [AppointmentController::class, 'store']
)->name('appointment.store');

Route::get(
    '/paiement/rendez-vous/{appointment}',
    [AppointmentController::class, 'paymentInvitation']
)
    ->middleware('signed')
    ->name('appointment.payment');

/*
 * Test écrit Soutien Lycée.
 * Le sujet est visible publiquement.
 */
Route::get(
    '/test-lycee/{subject}/{level}/{class}',
    [HighSchoolTestController::class, 'show']
)->name('high-school-test.show');

/*
 * Les tests d'admission sont publics.
 * Le visiteur passe d'abord le test, envoie ensuite son rendez-vous,
 * puis crée son compte étudiant.
 */
Route::post(
    '/test-lycee/{subject}/{level}/{class}',
    [HighSchoolTestController::class, 'store']
)
    ->middleware('throttle:10,1')
    ->name('high-school-test.store');

Route::get(
    '/test-vocal/{subject}/{level}/{class}',
    [VocalTestController::class, 'create']
)->name('vocal-test.create');

Route::post(
    '/test-vocal/{subject}/{level}/{class}',
    [VocalTestController::class, 'store']
)
    ->middleware('throttle:10,1')
    ->name('vocal-test.store');

Route::middleware('auth')->group(function () {
    Route::get(
        '/tests-lycee/soumissions/{submission}/images/{index}',
        [HighSchoolTestController::class, 'image']
    )
        ->where('index', '[0-9]+')
        ->name('high-school-test.image');
});

Route::get(
    '/classes',
    [HomeController::class, 'classes']
)->name('front.classes');

Route::get(
    '/niveaux',
    [HomeController::class, 'niveaux']
)->name('front.niveaux');

Route::get(
    '/matieres/{id}/classes',
    [FrontController::class, 'subjectClasses']
)->name('front.subject.classes');

Route::get(
    '/matieres/{id}/levels',
    [FrontController::class, 'subjectLevels']
)->name('front.subject.levels');

Route::get(
    '/matieres/{subject}/levels/{level}/classes',
    [FrontController::class, 'levelClasses']
)->name('front.subject.level.classes');

Route::get(
    '/matieres/{subject}/levels/{level}/classes/{class}/courses',
    [FrontController::class, 'courses']
)->name('front.courses');

Route::get(
    '/levels/{id}/courses',
    [FrontController::class, 'levelCourses']
)->name('front.level.courses');

Route::get(
    '/course/{id}',
    [FrontController::class, 'showCourse']
)->name('front.course.show');

Route::get(
    '/course/{course}/resource/{type}',
    [CourseResourceController::class, 'show']
)
    ->where('type', 'video|pdf|link')
    ->middleware('throttle:60,1')
    ->name('course.resource');

// Navigation publique : Niveaux → Classes → Matières → Cours
Route::get(
    '/classes/{level}/classes',
    [FrontController::class, 'publicClasses']
)->name('front.public.classes');

Route::get(
    '/classes/{level}/classes/{class_room}/subjects',
    [FrontController::class, 'publicSubjects']
)->name('front.public.subjects');

Route::get(
    '/classes/{level}/classes/{class_room}/subjects/{subject}/courses',
    [FrontController::class, 'publicCourses']
)->name('front.public.courses');

Route::get(
    '/religieux',
    [FrontController::class, 'religieux']
)->name('front.religieux');

Route::get(
    '/scolaires',
    [FrontController::class, 'scolaires']
)->name('front.scolaires');

Route::get(
    '/all-classes-courses',
    [HomeController::class, 'allClassesCourses']
)->name('front.all-classes-courses');

Route::get(
    '/lives',
    [HomeController::class, 'lives']
)->name('front.lives');

/*
|--------------------------------------------------------------------------
| ACCÈS SÉCURISÉ AUX LIVES
|--------------------------------------------------------------------------
|
| 1. Vérification de la connexion, du paiement, de la classe et de l'horaire.
| 2. Redirection par une URL Laravel signée et temporaire.
|
*/

Route::middleware([
    'auth',
    'throttle:30,1',
])->group(function () {
    Route::get(
        '/lives/{live}/access',
        [LiveAccessController::class, 'requestAccess']
    )->name('live.access.request');

    Route::get(
        '/lives/{live}/join',
        [LiveAccessController::class, 'join']
    )
        ->middleware([
            'signed',
            'throttle:10,1',
        ])
        ->name('live.join.signed');
});

Route::get('/account/blocked', function () {
    return view('auth.account-blocked');
})->name('account.blocked');

Route::post(
    '/logout',
    [AuthenticatedSessionController::class, 'destroy']
)->name('logout');

/*
|--------------------------------------------------------------------------
| LEARNING (NIVEAUX / COURS / TESTS)
|--------------------------------------------------------------------------
*/

// PUBLIC (visiteur)
Route::get(
    '/levels',
    [LearningController::class, 'levels']
)->name('levels');

Route::get(
    '/levels/{level}',
    [LearningController::class, 'courses']
)->name('levels.courses');

// PROTECTED (login obligatoire)
Route::middleware('auth')->group(function () {
    Route::post(
        '/course/{id}/test',
        [LearningController::class, 'submitTest']
    )->name('course.test');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

/*
|---------------------------
| COURSES (ADMIN + VALIDATION PROF)
|---------------------------
*/
Route::middleware(['auth', 'isAdmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        /*
         * L'administration conserve la création directe des cours.
         * Les propositions professeur arrivent dans la même liste,
         * avec validation / refus.
         */
        Route::post(
            '/courses/{course}/approve',
            [CourseController::class, 'approve']
        )->name('courses.approve');

        Route::post(
            '/courses/{course}/reject',
            [CourseController::class, 'reject']
        )->name('courses.reject');

        Route::resource(
            'courses',
            CourseController::class
        );

        // AJAX : récupérer les matières d'une classe
        Route::get(
            '/get-class-subjects/{classId}',
            [CourseController::class, 'getClassSubjects']
        )->name('get-class-subjects');
    });

/*
|---------------------------
| ADMIN ONLY
|---------------------------
*/
Route::middleware(['auth', 'isAdmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name('dashboard');

        Route::prefix('professors')
            ->name('professors.')
            ->group(function () {
                Route::get(
                    '/',
                    [ProfessorController::class, 'index']
                )->name('index');

                Route::get(
                    '/create',
                    [ProfessorController::class, 'create']
                )->name('create');

                Route::post(
                    '/',
                    [ProfessorController::class, 'store']
                )->name('store');

                Route::post(
                    '/{professor}/resend',
                    [ProfessorController::class, 'resend']
                )->name('resend');
            });

        // Disponibilités professeurs : créneaux fixes de 1h30
        Route::get(
            '/professor-availability',
            [ProfessorAvailabilityController::class, 'index']
        )->name('professor-availability.index');

        Route::put(
            '/professor-availability/{professor}',
            [ProfessorAvailabilityController::class, 'update']
        )->name('professor-availability.update');

        Route::delete(
            '/professor-availability/{professor}',
            [ProfessorAvailabilityController::class, 'destroy']
        )->name('professor-availability.destroy');

        Route::resource(
            'devoirs',
            DevoirController::class
        )->except(['show']);

        Route::resource(
            'classes',
            ClassController::class
        )->except(['show']);

        // Navigation hiérarchique : Matières → Niveaux → Classes → Cours
        Route::get(
            '/subjects',
            [LevelController::class, 'subjectsIndex']
        )->name('subjects.index');

        Route::post(
            '/subjects/hierarchy',
            [LevelController::class, 'storeSubjectHierarchy']
        )->name('subjects.hierarchy.store');

        /*
        |--------------------------------------------------------------------------
        | CRUD MATIÈRES / NIVEAUX / CLASSES
        |--------------------------------------------------------------------------
        |
        | Ces routes restent dans le groupe admin déjà protégé par :
        | auth + isAdmin + prefix('admin') + name('admin.')
        |
        */

        // ================================
        // MATIÈRES
        // ================================

        Route::get(
            '/subjects/{subject}/edit',
            [LevelController::class, 'editSubject']
        )->name('subjects.edit');

        Route::patch(
            '/subjects/{subject}',
            [LevelController::class, 'updateSubject']
        )->name('subjects.update');

        Route::delete(
            '/subjects/{subject}',
            [LevelController::class, 'destroySubject']
        )->name('subjects.destroy');


        // ================================
        // NIVEAUX / PARCOURS
        // ================================

        Route::post(
            '/subjects/{subject}/levels',
            [LevelController::class, 'storeSubjectLevel']
        )->name('subjects.levels.store');

        Route::get(
            '/subjects/{subject}/levels/{level}/edit',
            [LevelController::class, 'editSubjectLevel']
        )->name('subjects.levels.edit');

        Route::patch(
            '/subjects/{subject}/levels/{level}',
            [LevelController::class, 'updateSubjectLevel']
        )->name('subjects.levels.update');

        Route::delete(
            '/subjects/{subject}/levels/{level}',
            [LevelController::class, 'destroySubjectLevel']
        )->name('subjects.levels.destroy');


        // ================================
        // CLASSES
        // ================================

        Route::post(
            '/subjects/{subject}/levels/{level}/classes',
            [LevelController::class, 'storeSubjectClass']
        )->name('subjects.classes.store');

        Route::get(
            '/subjects/{subject}/levels/{level}/classes/{class}/edit',
            [LevelController::class, 'editSubjectClass']
        )->name('subjects.classes.edit');

        Route::patch(
            '/subjects/{subject}/levels/{level}/classes/{class}',
            [LevelController::class, 'updateSubjectClass']
        )->name('subjects.classes.update');

        Route::delete(
            '/subjects/{subject}/levels/{level}/classes/{class}',
            [LevelController::class, 'destroySubjectClass']
        )->name('subjects.classes.destroy');

        Route::get(
            '/subjects/{subject}/levels',
            [LevelController::class, 'subjectLevels']
        )->name('subjects.levels');

        Route::get(
            '/subjects/{subject}/levels/{level}/classes',
            [LevelController::class, 'subjectClasses']
        )->name('subjects.classes');

        Route::get(
            '/subjects/{subject}/levels/{level}/classes/{class}/courses',
            [LevelController::class, 'subjectCourses']
        )->name('subjects.courses');

        // Ancienne navigation Niveaux → Classes
        Route::get(
            '/levels/{level}/classes',
            [LevelController::class, 'classes']
        )->name('levels.classes');

        Route::get(
            '/levels/{level}/classes/{class}/subjects',
            [LevelController::class, 'subjects']
        )->name('levels.subjects');

        Route::get(
            '/levels/{level}/classes/{class}/subjects/{subject}/courses',
            [LevelController::class, 'courses']
        )->name('levels.courses');

        Route::post(
            '/levels/{level}/classes/{class}/subjects/attach',
            [LevelController::class, 'attachSubject']
        )->name('levels.subjects.attach');

        Route::delete(
            '/levels/{level}/classes/{class}/subjects/{subject}/detach',
            [LevelController::class, 'detachSubject']
        )->name('levels.subjects.detach');

        Route::resource(
            'levels',
            LevelController::class
        )->except(['create', 'edit', 'show']);

        Route::get(
            'users/without-class',
            [UserController::class, 'withoutClass']
        )->name('users.without-class');

        Route::resource(
            'users',
            UserController::class
        )
            ->except(['create', 'store'])
            ->where(['user' => '[0-9]+']);

        Route::get(
            'users/{user}/test-results',
            [UserController::class, 'testResults']
        )->name('users.test-results');

        Route::get(
            'users/{userId}/test/{testId}/result',
            [UserController::class, 'showResult']
        )->name('users.test-result');

        Route::put(
            'users/{user}/activate',
            [UserController::class, 'activate']
        )->name('users.activate');

        Route::put(
            'users/{user}/deactivate',
            [UserController::class, 'deactivate']
        )->name('users.deactivate');

        // Navigation hiérarchique : Matières → Niveaux → Classes → Lives
        Route::get(
            '/lives/subjects/{subject}/levels/{level}/classes/{class}',
            [LiveController::class, 'classLives']
        )->name('lives.class-lives');

        Route::get(
            '/lives/subjects/{subject}/levels/{level}/classes',
            [LiveController::class, 'subjectClasses']
        )->name('lives.subject-classes');

        Route::get(
            '/lives/subjects/{subject}/levels',
            [LiveController::class, 'subjectLevels']
        )->name('lives.subject-levels');

        Route::get(
            '/lives',
            [LiveController::class, 'index']
        )->name('lives.index');

        Route::get(
            '/lives/create',
            [LiveController::class, 'create']
        )->name('lives.create');

        Route::post(
            '/lives',
            [LiveController::class, 'store']
        )->name('lives.store');

        Route::get(
            '/lives/{live}/edit',
            [LiveController::class, 'edit']
        )->name('lives.edit');

        Route::put(
            '/lives/{live}',
            [LiveController::class, 'update']
        )->name('lives.update');

        Route::delete(
            '/lives/{live}',
            [LiveController::class, 'destroy']
        )->name('lives.destroy');

        // Planning hebdomadaire des classes
        Route::get(
            '/schedule/events',
            [AdminScheduleController::class, 'events']
        )->name('schedule.events');

        Route::get(
            '/schedule',
            [AdminScheduleController::class, 'index']
        )->name('schedule.index');

        Route::post(
            '/schedule',
            [AdminScheduleController::class, 'store']
        )->name('schedule.store');

        Route::put(
            '/schedule/{schedule}',
            [AdminScheduleController::class, 'update']
        )->name('schedule.update');

        Route::delete(
            '/schedule/{schedule}',
            [AdminScheduleController::class, 'destroy']
        )->name('schedule.destroy');

        Route::get(
            '/chat',
            [ChatController::class, 'adminIndex']
        )->name('chat.list');

        Route::get(
            '/chat/{subject}',
            [ChatController::class, 'adminChat']
        )->name('chat');

        Route::post(
            '/chat/send',
            [ChatController::class, 'adminSend']
        )->name('chat.send');

        Route::delete(
            '/chat/delete',
            [ChatController::class, 'adminDelete']
        )->name('chat.delete');

        // Absences
        Route::get(
            '/absences',
            [DashboardController::class, 'absences']
        )->name('absences');

        Route::get(
            '/absences/create',
            [DashboardController::class, 'create']
        )->name('absences.create');

        Route::post(
            '/absences',
            [DashboardController::class, 'store']
        )->name('absences.store');

        Route::get(
            '/absences/{absence}',
            [DashboardController::class, 'show']
        )->name('absences.show');

        Route::get(
            '/absences/{absence}/edit',
            [DashboardController::class, 'edit']
        )->name('absences.edit');

        Route::put(
            '/absences/{absence}',
            [DashboardController::class, 'update']
        )->name('absences.update');

        Route::delete(
            '/absences/{absence}',
            [DashboardController::class, 'destroy']
        )->name('absences.destroy');

        // Assign class
        Route::get(
            '/assign-class',
            [UserController::class, 'assignClass']
        )->name('assign.class');

        Route::post(
            '/assign-class',
            [UserController::class, 'storeAssignment']
        )->name('assign.class.store');

        Route::patch(
            '/assign-class/{pivot}',
            [UserController::class, 'updateAssignment']
        )->name('assign.class.update');

        Route::delete(
            '/assign-class/{pivot}',
            [UserController::class, 'destroyAssignment']
        )->name('assign.class.destroy');

        // Assignation des professeurs
        Route::get(
            '/prof-assignments',
            [UserController::class, 'profAssignments']
        )->name('users.prof-assignments');

        Route::post(
            '/prof-assignments',
            [UserController::class, 'storeProfAssignment']
        )->name('users.store-prof-assignment');

        Route::delete(
            '/prof-assignments/{id}',
            [UserController::class, 'destroyProfAssignment']
        )->name('users.destroy-prof-assignment');

        // Tests vocaux
        Route::prefix('vocal-tests')
            ->name('vocal-tests.')
            ->group(function () {
                Route::resource(
                    'prompts',
                    \App\Http\Controllers\Admin\VocalTestPromptController::class
                )->except(['show']);

                Route::get(
                    '/submissions',
                    [
                        \App\Http\Controllers\Admin\VocalTestSubmissionController::class,
                        'index'
                    ]
                )->name('submissions.index');

                Route::get(
                    '/submissions/{submission}',
                    [
                        \App\Http\Controllers\Admin\VocalTestSubmissionController::class,
                        'show'
                    ]
                )->name('submissions.show');

                Route::post(
                    '/submissions/{submission}/review',
                    [
                        \App\Http\Controllers\Admin\VocalTestSubmissionController::class,
                        'review'
                    ]
                )->name('submissions.review');

                Route::get(
                    '/submissions/{submission}/audio',
                    [
                        \App\Http\Controllers\Admin\VocalTestSubmissionController::class,
                        'audio'
                    ]
                )->name('submissions.audio');

                Route::delete(
                    '/submissions/{submission}',
                    [
                        \App\Http\Controllers\Admin\VocalTestSubmissionController::class,
                        'destroy'
                    ]
                )->name('submissions.destroy');

                Route::post(
                    '/submissions/{submission}/professors',
                    [
                        \App\Http\Controllers\Admin\VocalTestSubmissionController::class,
                        'assignProfessors'
                    ]
                )->name('submissions.professors');
            });

        // Centre de correction des tests écrits
        Route::prefix('written-tests')
            ->name('written-tests.')
            ->group(function () {
                Route::get(
                    '/',
                    [HighSchoolTestReviewController::class, 'index']
                )->name('index');

                Route::get(
                    '/{submission}',
                    [HighSchoolTestReviewController::class, 'show']
                )->name('show');

                Route::patch(
                    '/{submission}',
                    [HighSchoolTestReviewController::class, 'update']
                )->name('update');

                Route::get(
                    '/{submission}/report',
                    [HighSchoolTestReviewController::class, 'report']
                )->name('report');
            });

        // Contacts / prospects issus du formulaire public
        Route::get(
            '/contacts',
            [ContactLeadController::class, 'index']
        )->name('contacts.index');

        Route::get(
            '/contacts/export/csv',
            [ContactLeadController::class, 'exportCsv']
        )->name('contacts.export');

        Route::get(
            '/contacts/{contact}',
            [ContactLeadController::class, 'show']
        )->name('contacts.show');

        // Rendez-vous
        Route::get(
            '/appointments',
            [AppointmentController::class, 'index']
        )->name('appointments.index');

        Route::patch(
            '/appointments/{appointment}/confirm',
            [AppointmentController::class, 'confirm']
        )->name('appointments.confirm');

        Route::patch(
            '/appointments/{appointment}/cancel',
            [AppointmentController::class, 'cancel']
        )->name('appointments.cancel');

        Route::delete(
            '/appointments/{appointment}',
            [AppointmentController::class, 'destroy']
        )->name('appointments.destroy');

        Route::get(
            '/appointments/{appointment}/audio',
            [AppointmentController::class, 'audio']
        )->name('appointments.audio');

        Route::post(
            '/appointments/{appointment}/payment-email',
            [AppointmentController::class, 'sendPaymentEmail']
        )->name('appointments.payment-email');

        Route::get('/profile', function () {
            return view('admin.profile');
        })->name('profile');

        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('settings');

        Route::put(
            '/settings/profile',
            [DashboardController::class, 'updateProfile']
        )->name('settings.profile.update');

        Route::put(
            '/settings/password',
            [DashboardController::class, 'updatePassword']
        )->name('settings.password.update');
    });

/*
|--------------------------------------------------------------------------
| PROF - UNIFIED GROUP
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'isProf',
    'force.prof.password',
])
    ->prefix('prof')
    ->name('prof.')
    ->group(function () {
        Route::get(
            '/premier-mot-de-passe',
            [FirstPasswordController::class, 'edit']
        )->name('password.first.edit');

        Route::put(
            '/premier-mot-de-passe',
            [FirstPasswordController::class, 'update']
        )->name('password.first.update');

        Route::get(
            '/dashboard',
            [ProfController::class, 'dashboard']
        )->name('dashboard');

        Route::get('/profile', function () {
            return view('prof.profile');
        })->name('profile');

        Route::get('/settings', function () {
            return view('prof.settings');
        })->name('settings');

        Route::put(
            '/settings/profile',
            [ProfController::class, 'updateProfile']
        )->name('settings.profile.update');

        Route::put(
            '/settings/password',
            [ProfController::class, 'updatePassword']
        )->name('settings.password.update');

        Route::resource(
            'courses',
            ProfCourseController::class
        );

        Route::get(
            '/chat/subjects',
            [ChatController::class, 'profSubjects']
        )->name('chat.subjects');

        Route::get(
            '/chat/{subject}',
            [ChatController::class, 'profChat']
        )->name('chat');

        Route::post(
            '/chat/send',
            [ChatController::class, 'profSend']
        )->name('chat.send');

        Route::delete(
            '/chat/delete',
            [ChatController::class, 'profDelete']
        )->name('chat.delete');

        Route::get(
            '/assignments',
            [ProfController::class, 'assignments']
        )->name('assignments');

        Route::post(
            '/grade',
            [ProfController::class, 'grade']
        )->name('grade');

        Route::get(
            '/absences',
            [ProfController::class, 'absences']
        )->name('absences');

        Route::get(
            '/class-students/{id}',
            [ProfController::class, 'getStudents']
        )->name('class.students');

        Route::post(
            '/absences/store',
            [ProfController::class, 'storeAbsence']
        )->name('absences.store');

        Route::get(
            '/absences/list',
            [ProfController::class, 'absencesList']
        )->name('absences.list');

        Route::put(
            '/absences/{id}',
            [ProfController::class, 'updateAbsence']
        )->name('absences.update');

        Route::get(
            '/lives',
            [ProfController::class, 'livesIndex']
        )->name('lives.index');

        Route::get(
            '/subjects',
            [ProfController::class, 'subjectsList']
        )->name('subjects.list');

        Route::get(
            '/subjects/{subject}/levels',
            [ProfController::class, 'subjectLevels']
        )->name('subjects.levels');

        Route::get(
            '/subjects/{subject}/levels/{level}/classes',
            [ProfController::class, 'subjectClasses']
        )->name('subjects.classes');

        Route::get(
            '/subjects/{subject}/levels/{level}/classes/{class}/courses',
            [ProfController::class, 'subjectCourses']
        )->name('subjects.courses');

        Route::get(
            '/subjects/{subject}/levels/{level}/classes/{class}/lives',
            [ProfController::class, 'subjectLives']
        )->name('subjects.lives');

        Route::get(
            '/subjects/{subject}/levels/{level}/classes/{class}/devoirs',
            [ProfController::class, 'subjectDevoirs']
        )->name('subjects.devoirs');

        Route::get(
            '/browse/{level}/{class}/courses/{subject}',
            [ProfLevelController::class, 'courses']
        )->name('browse.courses');

        Route::get(
            '/browse/{level}/{class}/lives',
            [ProfController::class, 'browseLives']
        )->name('browse.lives');

        Route::get(
            '/browse/{level}/{class}/devoirs/{subject}',
            [ProfController::class, 'browseDevoirs']
        )->name('browse.devoirs');

        Route::resource(
            'devoir',
            ProfDevoirController::class
        )
            ->except(['show'])
            ->names([
                'index' => 'devoir.index',
                'create' => 'devoir.create',
                'store' => 'devoir.store',
                'edit' => 'devoir.edit',
                'update' => 'devoir.update',
                'destroy' => 'devoir.destroy',
            ]);

        Route::get(
            '/schedule',
            [ScheduleController::class, 'index']
        )->name('schedule');

        Route::get(
            '/schedule/data',
            [ScheduleController::class, 'data']
        )->name('schedule.data');

        Route::get(
            '/classes',
            [ScheduleController::class, 'classes']
        )->name('classes');

        Route::post(
            '/schedule/update',
            [ScheduleController::class, 'update']
        )->name('schedule.update');

        Route::post(
            '/schedule/store',
            [ScheduleController::class, 'store']
        )->name('schedule.store');

        Route::delete(
            '/schedule/{id}',
            [ScheduleController::class, 'destroy']
        )->name('schedule.destroy');

        /*
         * Tests vocaux affectés au professeur par l'administration.
         */
        Route::get(
            '/vocal-tests',
            [
                \App\Http\Controllers\Prof\VocalTestSubmissionController::class,
                'index'
            ]
        )->name('vocal-tests.index');

        Route::get(
            '/vocal-tests/{submission}',
            [
                \App\Http\Controllers\Prof\VocalTestSubmissionController::class,
                'show'
            ]
        )->name('vocal-tests.show');

        Route::get(
            '/vocal-tests/{submission}/audio',
            [
                \App\Http\Controllers\Prof\VocalTestSubmissionController::class,
                'audio'
            ]
        )->name('vocal-tests.audio');
    });

/*
|--------------------------------------------------------------------------
| STUDENT (PROTECTED READ-ONLY)
|--------------------------------------------------------------------------
*/

// Routes étudiantes SANS vérification de paiement
Route::middleware(['auth'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get(
            '/levels',
            [StudentController::class, 'levels']
        )->name('levels');

        Route::get(
            '/levels/{level}/classes',
            [StudentController::class, 'levelClasses']
        )->name('levels.classes');

        Route::get(
            '/levels/{level}/classes/{class}/subjects',
            [StudentController::class, 'levelSubjects']
        )->name('levels.subjects');

        Route::get(
            '/subjects',
            [StudentController::class, 'indexSubjects']
        )->name('subjects.index');

        Route::get(
            '/subjects/{subject}/levels',
            [StudentController::class, 'subjectLevels']
        )->name('subjects.levels');

        Route::get(
            '/subjects/{subject}/levels/{level}/classes',
            [StudentController::class, 'subjectClasses']
        )->name('subjects.classes');

        Route::get(
            '/subjects/{subject}/levels/{level}/classes/{class}/courses',
            [StudentController::class, 'subjectCourses']
        )
            ->middleware(['active', 'paid'])
            ->name('subjects.courses');

        Route::get(
            '/subjects/{level}',
            [StudentController::class, 'subjects']
        )->name('subjects');

        Route::get(
            '/classes/{subject}/{level}',
            [StudentController::class, 'classes']
        )->name('classes');

        Route::get(
            '/waiting',
            [StudentController::class, 'waiting']
        )->name('waiting');

        // Historique des tests écrits Soutien Lycée
        Route::get(
            '/written-tests',
            [HighSchoolTestHistoryController::class, 'index']
        )->name('written-tests.index');

        Route::get(
            '/written-tests/{submission}',
            [HighSchoolTestHistoryController::class, 'show']
        )->name('written-tests.show');

        Route::get(
            '/written-tests/{submission}/report',
            [HighSchoolTestHistoryController::class, 'report']
        )->name('written-tests.report');

        // Tests QCM
        Route::get(
            '/tests',
            [StudentTestController::class, 'index']
        )->name('tests.index');

        Route::get(
            '/tests/{test}',
            [StudentTestController::class, 'show']
        )->name('tests.show');

        Route::post(
            '/tests/{test}',
            [StudentTestController::class, 'submit']
        )->name('tests.submit');
    });

// Routes étudiantes protégées (nécessite un compte actif)
Route::middleware(['auth', 'active'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get(
            '/dashboard',
            [StudentController::class, 'dashboard']
        )->name('dashboard');

        /*
         * L'emploi du temps est intégré à l'interface Lives.
         */
        Route::get('/planning', function () {
            return redirect()->route(
                'student.lives',
                request()->query()
            );
        })->name('schedule.index');

        Route::get(
            '/lives',
            [StudentController::class, 'lives']
        )->name('lives');

        // Cours
        Route::get(
            '/courses/{subject}/{class}',
            [StudentController::class, 'courses']
        )->name('courses');

        Route::get(
            '/course/{id}',
            [StudentController::class, 'showCourse']
        )
            ->middleware('paid')
            ->name('course.show');

        /*
         * Protection anti-partage :
         * un seul appareil étudiant peut consommer les contenus sensibles.
         */
        Route::post(
            '/content-session/courses/{course}/heartbeat',
            [ContentSessionController::class, 'heartbeat']
        )
            ->middleware('throttle:120,1')
            ->name('content-session.course.heartbeat');

        Route::post(
            '/content-session/courses/{course}/release',
            [ContentSessionController::class, 'release']
        )
            ->middleware('throttle:30,1')
            ->name('content-session.course.release');

        Route::post(
            '/content-session/release-device',
            [ContentSessionController::class, 'releaseDevice']
        )
            ->middleware('throttle:10,1')
            ->name('content-session.release-device');

        // Chats
        Route::get(
            '/chats',
            [ChatController::class, 'subjects']
        )->name('chats');

        Route::post(
            '/chat/send',
            [ChatController::class, 'send']
        )->name('chat.send');

        Route::get(
            '/chat/{subject}',
            [ChatController::class, 'index']
        )->name('student.chat');

        Route::delete(
            '/chat/delete',
            [ChatController::class, 'delete']
        )->name('chat.delete');

        // Assignments
        Route::get(
            '/assignments',
            [StudentController::class, 'assignments']
        )->name('assignments');

        Route::post(
            '/assignments/send',
            [StudentController::class, 'sendAssignment']
        )->name('assignments.send');

        // Absences
        Route::get(
            '/absences',
            [StudentController::class, 'absences']
        )->name('absences');

        // Profile & Settings
        Route::get(
            '/profile',
            [StudentController::class, 'profile']
        )->name('profile');

        Route::get(
            '/settings',
            [StudentController::class, 'settings']
        )->name('settings');

        Route::put(
            '/settings/profile',
            [StudentController::class, 'updateProfile']
        )->name('settings.profile.update');

        Route::put(
            '/settings/password',
            [StudentController::class, 'updatePassword']
        )->name('settings.password.update');
    });

/*
|--------------------------------------------------------------------------
| PARENT
|--------------------------------------------------------------------------
*/
require __DIR__ . '/notifications.php';
require __DIR__ . '/parent.php';

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| PLANS / OFFRES
|--------------------------------------------------------------------------
*/
Route::get(
    '/plans',
    [PlanController::class, 'index']
)->name('plans');

Route::get(
    '/paypal/checkout',
    [PaymentController::class, 'paypalCheckout']
)->name('paypal.checkout');

Route::get(
    '/payment',
    [PaymentController::class, 'index']
)->name('student.payment');

Route::post(
    '/checkout',
    [PaymentController::class, 'checkout']
)->name('student.checkout');