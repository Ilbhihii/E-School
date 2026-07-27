<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Subject;
use App\Models\Level;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Live;
use App\Models\TestAppointment;
use App\Models\VocalTestSubmission;
use App\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected User $prof;
    protected Subject $subject;
    protected Subject $subjectCoran;
    protected Level $level;
    protected ClassRoom $classRoom;
    protected Course $course;
    protected Live $live;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer des utilisateurs
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
        $this->prof = User::factory()->prof()->create([
            'is_active' => true,
        ]);
        $this->student = User::factory()->student()->create([
            'is_active' => true,
        ]);

        // Créer les matières principales
        $this->subject = Subject::factory()->create([
            'name' => 'Arabe',
            'type' => 'scolaire',
        ]);
        $this->subjectCoran = Subject::factory()->create([
            'name' => 'Coran',
            'type' => 'religieux',
        ]);

        // Créer un niveau
        $this->level = Level::factory()->create([
            'name' => '1ère année',
            'subject_id' => $this->subject->id,
            'order' => 1,
        ]);

        // Créer une classe
        $this->classRoom = ClassRoom::factory()->create([
            'name' => 'Classe A1',
            'level_id' => $this->level->id,
        ]);

        // Associer la matière à la classe
        $this->classRoom->subjects()->attach($this->subject->id);

        // Créer un cours
        $this->course = Course::factory()->create([
            'title' => 'Introduction à l\'Arabe',
            'description' => 'Les bases de la langue arabe',
            'subject_id' => $this->subject->id,
            'level_id' => $this->level->id,
            'class_id' => $this->classRoom->id,
            'is_free' => true,
            'admin_id' => $this->admin->id,
        ]);

        // Créer un live
        $this->live = Live::factory()->create([
            'title' => 'Cours en direct',
            'class_id' => $this->classRoom->id,
            'admin_id' => $this->admin->id,
            'live_date' => now()->addDays(1),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ]);
    }

    /* ══════════════════════════════════════════════════════════════
       TESTS — ENDPOINTS PUBLICS
       ══════════════════════════════════════════════════════════════ */

    /** @test */
    public function public_can_list_subjects()
    {
        $response = $this->getJson('/api/subjects');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'type', 'courses_count', 'levels_count', 'classes_count']
                ]
            ]);
    }

    /** @test */
    public function public_can_view_subject()
    {
        $response = $this->getJson("/api/subjects/{$this->subject->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Arabe');
    }

    /** @test */
    public function public_can_view_subject_levels()
    {
        $response = $this->getJson("/api/subjects/{$this->subject->id}/levels");

        $response->assertStatus(200)
            ->assertJsonPath('data.subject.name', 'Arabe')
            ->assertJsonPath('data.levels.0.name', '1ère année');
    }

    /** @test */
    public function public_can_view_level()
    {
        $response = $this->getJson("/api/levels/{$this->level->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', '1ère année')
            ->assertJsonPath('data.subject.name', 'Arabe');
    }

    /** @test */
    public function public_can_view_level_classes()
    {
        $response = $this->getJson("/api/levels/{$this->level->id}/classes");

        $response->assertStatus(200)
            ->assertJsonPath('data.level.name', '1ère année')
            ->assertJsonPath('data.classes.0.name', 'Classe A1');
    }

    /** @test */
    public function public_can_view_class()
    {
        $response = $this->getJson("/api/classes/{$this->classRoom->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Classe A1')
            ->assertJsonStructure(['data' => ['id', 'name', 'level', 'courses_count', 'subjects_count']]);
    }

    /** @test */
    public function public_can_view_class_courses()
    {
        $response = $this->getJson("/api/classes/{$this->classRoom->id}/courses");

        $response->assertStatus(200)
            ->assertJsonPath('data.0.title', 'Introduction à l\'Arabe');
    }

    /** @test */
    public function public_can_view_class_subjects()
    {
        $response = $this->getJson("/api/classes/{$this->classRoom->id}/subjects");

        $response->assertStatus(200)
            ->assertJsonPath('data.0.name', 'Arabe');
    }

    /** @test */
    public function public_can_list_courses()
    {
        $response = $this->getJson('/api/courses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'subject', 'level', 'is_free']
                ]
            ]);
    }

    /** @test */
    public function public_can_filter_courses_by_subject()
    {
        $response = $this->getJson('/api/courses?subject_id=' . $this->subject->id);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function public_can_view_course()
    {
        $response = $this->getJson("/api/courses/{$this->course->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Introduction à l\'Arabe')
            ->assertJsonStructure(['data' => ['id', 'title', 'description', 'subject', 'level', 'class', 'is_free']]);
    }

    /** @test */
    public function public_can_list_lives()
    {
        $response = $this->getJson('/api/lives');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.title', 'Cours en direct');
    }

    /** @test */
    public function public_can_list_upcoming_lives()
    {
        $response = $this->getJson('/api/lives/upcoming');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function public_can_get_home_stats()
    {
        $response = $this->getJson('/api/home/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['total_classes', 'total_courses', 'total_subjects', 'upcoming_lives']
            ])
            ->assertJsonPath('data.total_courses', 1)
            ->assertJsonPath('data.total_classes', 1);
    }

    /** @test */
    public function public_can_get_appointment_types()
    {
        $response = $this->getJson('/api/appointments/types');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [['value', 'label']]
            ]);
    }

    /** @test */
    public function public_can_get_vocal_test_text()
    {
        $response = $this->getJson('/api/vocal-test/text');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['text', 'source']]);
    }

    /* ══════════════════════════════════════════════════════════════
       TESTS — AUTHENTIFICATION
       ══════════════════════════════════════════════════════════════ */

    /** @test */
    public function user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Nouvel Étudiant',
            'email' => 'nouveau@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['user', 'token']
            ])
            ->assertJsonPath('data.user.name', 'Nouvel Étudiant');

        $this->assertDatabaseHas('users', ['email' => 'nouveau@test.com']);
    }

    /** @test */
    public function user_can_login()
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->student->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['user', 'token']
            ])
            ->assertJsonPath('data.user.email', $this->student->email);
    }

    /** @test */
    public function login_fails_with_wrong_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->student->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function login_fails_for_inactive_account()
    {
        $inactive = User::factory()->student()->create(['is_active' => false]);

        $response = $this->postJson('/api/login', [
            'email' => $inactive->email,
            'password' => 'password',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function authenticated_user_can_get_profile()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonPath('data.email', $this->student->email)
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role']]);
    }

    /** @test */
    public function unauthenticated_user_cannot_get_profile()
    {
        $response = $this->getJson('/api/profile');

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_update_profile()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->putJson('/api/profile', [
                'name' => 'Nom Mis à Jour',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Nom Mis à Jour');
    }

    /** @test */
    public function user_can_logout()
    {
        $token = $this->student->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    /* ══════════════════════════════════════════════════════════════
       TESTS — ENDPOINTS PROTÉGÉS
       ══════════════════════════════════════════════════════════════ */

    /** @test */
    public function authenticated_user_can_view_dashboard()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['user', 'stats', 'available_subjects', 'recent_courses', 'upcoming_lives']
            ]);
    }

    /** @test */
    public function authenticated_user_can_mark_course_complete()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson("/api/courses/{$this->course->id}/complete", [
                'score' => 85,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.completed', true)
            ->assertJsonPath('data.score', 85);

        $this->assertDatabaseHas('user_progress', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'completed' => true,
            'score' => 85,
        ]);
    }

    /** @test */
    public function authenticated_user_can_view_progress()
    {
        // D'abord marquer quelques progrès
        UserProgress::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'completed' => true,
            'score' => 90,
        ]);

        $response = $this->actingAs($this->student, 'sanctum')
            ->getJson('/api/progress');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['total_courses', 'completed_courses', 'completion_percentage', 'recent_progress']
            ])
            ->assertJsonPath('data.completed_courses', 1);
    }

    /** @test */
    public function authenticated_user_can_view_progress_by_subject()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->getJson('/api/progress/by-subject');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [['subject_id', 'subject_name', 'total_courses', 'completed_courses', 'completion_percentage']]
            ]);
    }

    /** @test */
    public function authenticated_user_can_mark_progress()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson("/api/progress/{$this->course->id}", [
                'score' => 100,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.completed', true);
    }

    /** @test */
    public function authenticated_user_can_view_their_lives()
    {
        // Assigner la classe à l'étudiant
        $this->student->update(['class_id' => $this->classRoom->id]);

        $response = $this->actingAs($this->student, 'sanctum')
            ->getJson('/api/user/lives');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function authenticated_user_can_create_appointment()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson('/api/appointments', [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'phone' => '+212600000000',
                'email' => 'jean@test.com',
                'city' => 'Casablanca',
                'country' => 'Maroc',
                'type' => 'information',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('test_appointments', [
            'email' => 'jean@test.com',
            'type' => 'information',
        ]);
    }

    /** @test */
    public function authenticated_user_can_list_appointments()
    {
        // Créer un rendez-vous
        TestAppointment::create([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'phone' => '+212600000000',
            'email' => $this->student->email,
            'city' => 'Casablanca',
            'country' => 'Maroc',
            'type' => 'information',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->student, 'sanctum')
            ->getJson('/api/appointments');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function authenticated_user_can_submit_vocal_test()
    {
        $level2 = Level::factory()->create(['subject_id' => $this->subjectCoran->id]);
        $class2 = ClassRoom::factory()->create(['level_id' => $level2->id]);
        $class2->subjects()->attach($this->subjectCoran->id);

        // Créer un vrai fichier WAV minimal (44 octets valides) pour passer la validation mimes
        $wavHeader = "RIFF".pack('V', 36)."WAVEfmt ".pack('V', 16).pack('v', 1).pack('v', 1)
                     .pack('V', 44100).pack('V', 88200).pack('v', 2).pack('v', 16)
                     ."data".pack('V', 0);
        $tempPath = sys_get_temp_dir() . '/test_' . uniqid() . '.wav';
        file_put_contents($tempPath, $wavHeader);
        $file = new \Illuminate\Http\UploadedFile($tempPath, 'recitation.wav', 'audio/wav', null, true);

        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson('/api/vocal-test/submit', [
                'subject_id' => $this->subjectCoran->id,
                'level_id' => $level2->id,
                'class_id' => $class2->id,
                'audio' => $file,
            ]);

        // Nettoyer
        @unlink($tempPath);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['id']]);
    }

    /** @test */
    public function authenticated_user_can_list_vocal_submissions()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->getJson('/api/vocal-test/submissions');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    /* ══════════════════════════════════════════════════════════════
       TESTS — VALIDATION ET SÉCURITÉ
       ══════════════════════════════════════════════════════════════ */

    /** @test */
    public function register_validates_required_fields()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function appointment_validates_required_fields()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson('/api/appointments', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'phone', 'email', 'city', 'country', 'type']);
    }

    /** @test */
    public function unauthenticated_requests_get_401()
    {
        $this->getJson('/api/dashboard')->assertStatus(401);
        $this->getJson('/api/progress')->assertStatus(401);
        $this->getJson('/api/appointments')->assertStatus(401);
        $this->postJson('/api/appointments')->assertStatus(401);
        $this->postJson('/api/logout')->assertStatus(401);
    }

    /** @test */
    public function returns_404_for_nonexistent_resources()
    {
        $this->getJson('/api/subjects/9999')->assertStatus(404);
        $this->getJson('/api/levels/9999')->assertStatus(404);
        $this->getJson('/api/classes/9999')->assertStatus(404);
        $this->getJson('/api/courses/9999')->assertStatus(404);
    }

    /* ══════════════════════════════════════════════════════════════
       TESTS — PHOTO DE PROFIL
       ══════════════════════════════════════════════════════════════ */

    /** @test */
    public function user_can_upload_profile_photo()
    {
        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->actingAs($this->student, 'sanctum')
            ->post('/api/profile/photo', [
                'profile_photo' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // Vérifier que la photo a été stockée
        $this->student->refresh();
        $this->assertNotNull($this->student->profile_photo);
    }

    /** @test */
    public function profile_photo_requires_valid_image()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->student, 'sanctum')
            ->post('/api/profile/photo', [
                'profile_photo' => $file,
            ], ['Accept' => 'application/json']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['profile_photo']);
    }

    /* ══════════════════════════════════════════════════════════════
       TESTS — MOT DE PASSE OUBLIÉ
       ══════════════════════════════════════════════════════════════ */

    /** @test */
    public function user_can_request_password_reset()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => $this->student->email,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    /** @test */
    public function forgot_password_validates_email()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /* ══════════════════════════════════════════════════════════════
       TESTS — INSCRIPTION (CAS LIMITES)
       ══════════════════════════════════════════════════════════════ */

    /** @test */
    public function user_can_register_as_prof()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Nouveau Professeur',
            'email' => 'prof@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'prof',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.user.role', 'prof');

        $this->assertDatabaseHas('users', [
            'email' => 'prof@test.com',
            'role' => 'prof',
        ]);
    }

    /** @test */
    public function register_fails_with_duplicate_email()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Doublon',
            'email' => $this->student->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function register_requires_password_confirmation()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertStatus(422);
    }

    /* ══════════════════════════════════════════════════════════════
       TESTS — COURS (CAS LIMITES)
       ══════════════════════════════════════════════════════════════ */

    /** @test */
    public function course_filter_returns_empty_when_no_match()
    {
        $response = $this->getJson('/api/courses?subject_id=9999');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /** @test */
    public function class_courses_filter_by_subject()
    {
        $response = $this->getJson("/api/classes/{$this->classRoom->id}/courses?subject_id={$this->subject->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function class_courses_returns_empty_for_wrong_subject()
    {
        $response = $this->getJson("/api/classes/{$this->classRoom->id}/courses?subject_id=9999");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /* ══════════════════════════════════════════════════════════════
       TESTS — PROFIL (MISE À JOUR)
       ══════════════════════════════════════════════════════════════ */

    /** @test */
    public function profile_update_persists_changes()
    {
        $this->actingAs($this->student, 'sanctum')
            ->putJson('/api/profile', ['name' => 'Nom Modifié']);

        $this->assertDatabaseHas('users', [
            'id' => $this->student->id,
            'name' => 'Nom Modifié',
        ]);
    }

    /** @test */
    public function profile_update_cannot_use_existing_email()
    {
        $other = User::factory()->student()->create();

        $response = $this->actingAs($this->student, 'sanctum')
            ->putJson('/api/profile', ['email' => $other->email]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function profile_update_can_change_password()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->putJson('/api/profile', [
                'password' => 'nouveaumdp123',
                'password_confirmation' => 'nouveaumdp123',
            ]);

        $response->assertStatus(200);
    }

    /* ══════════════════════════════════════════════════════════════
       TESTS — LIVES (CAS LIMITES)
       ══════════════════════════════════════════════════════════════ */

    /** @test */
    public function upcoming_lives_empty_when_none_scheduled()
    {
        // Supprimer le live créé dans setUp
        $this->live->delete();

        $response = $this->getJson('/api/lives/upcoming');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /** @test */
    public function live_is_not_upcoming_when_in_past()
    {
        Live::factory()->create([
            'title' => 'Live passé',
            'class_id' => $this->classRoom->id,
            'admin_id' => $this->admin->id,
            'live_date' => now()->subDays(2),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ]);

        $response = $this->getJson('/api/lives/upcoming');

        // Seul le live du setUp (futur) doit apparaître
        $response->assertJsonCount(1, 'data');
    }

    /* ══════════════════════════════════════════════════════════════
       TESTS — FLUX COMPLET (RENDEZ-VOUS + TEST VOCAL)
       ══════════════════════════════════════════════════════════════ */

    /** @test */
    public function appointment_can_be_linked_to_vocal_submission()
    {
        $level2 = Level::factory()->create(['subject_id' => $this->subjectCoran->id]);
        $class2 = ClassRoom::factory()->create(['level_id' => $level2->id]);
        $class2->subjects()->attach($this->subjectCoran->id);

        // 1. Créer une soumission vocale directement
        $submission = VocalTestSubmission::create([
            'user_id' => $this->student->id,
            'subject_id' => $this->subjectCoran->id,
            'level_id' => $level2->id,
            'class_id' => $class2->id,
            'recitation_text' => 'بِسْمِ اللَّهِ',
            'audio_path' => 'vocal-tests/test.webm',
        ]);

        // 2. Créer un rendez-vous lié à cette soumission
        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson('/api/appointments', [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'phone' => '+212600000000',
                'email' => 'jean@test.com',
                'city' => 'Casablanca',
                'country' => 'Maroc',
                'type' => 'test',
                'vocal_test_submission_id' => $submission->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        // Vérifier que la soumission a été consommée
        $this->assertDatabaseHas('vocal_test_submissions', [
            'id' => $submission->id,
        ]);
        $this->assertNotNull($submission->fresh()->consumed_at);
    }

    /** @test */
    public function cannot_create_appointment_with_already_consumed_vocal()
    {
        $level2 = Level::factory()->create(['subject_id' => $this->subjectCoran->id]);
        $class2 = ClassRoom::factory()->create(['level_id' => $level2->id]);
        $class2->subjects()->attach($this->subjectCoran->id);

        // Soumission déjà consommée
        $submission = VocalTestSubmission::create([
            'user_id' => $this->student->id,
            'subject_id' => $this->subjectCoran->id,
            'level_id' => $level2->id,
            'class_id' => $class2->id,
            'recitation_text' => 'بِسْمِ اللَّهِ',
            'audio_path' => 'vocal-tests/test.webm',
            'consumed_at' => now(),
        ]);

        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson('/api/appointments', [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'phone' => '+212600000000',
                'email' => 'jean@test.com',
                'city' => 'Casablanca',
                'country' => 'Maroc',
                'type' => 'test',
                'vocal_test_submission_id' => $submission->id,
            ]);

        $response->assertStatus(404);
    }

    /* ══════════════════════════════════════════════════════════════
       TESTS — DASHBOARD (ROLES)
       ══════════════════════════════════════════════════════════════ */

    /** @test */
    public function dashboard_works_for_prof_role()
    {
        $response = $this->actingAs($this->prof, 'sanctum')
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonPath('data.user.role', 'prof');
    }

    /** @test */
    public function dashboard_works_for_admin_role()
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonPath('data.user.role', 'admin');
    }

    /* ══════════════════════════════════════════════════════════════
       TESTS — COURS AVEC PROGRESSION
       ══════════════════════════════════════════════════════════════ */

    /** @test */
    public function progress_shows_zero_when_no_courses_completed()
    {
        $response = $this->actingAs($this->student, 'sanctum')
            ->getJson('/api/progress');

        $response->assertStatus(200)
            ->assertJsonPath('data.completed_courses', 0);
    }

    /** @test */
    public function can_mark_same_course_complete_twice_updates_score()
    {
        // Première complétion
        $this->actingAs($this->student, 'sanctum')
            ->postJson("/api/progress/{$this->course->id}", ['score' => 50]);

        // Seconde complétion avec nouveau score
        $response = $this->actingAs($this->student, 'sanctum')
            ->postJson("/api/progress/{$this->course->id}", ['score' => 95]);

        $response->assertStatus(200)
            ->assertJsonPath('data.score', 95);

        $this->assertDatabaseHas('user_progress', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'score' => 95,
        ]);
    }
}
