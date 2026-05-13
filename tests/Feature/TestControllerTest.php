<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Test;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Result;
use App\Models\Subject;
use App\Services\QCMGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Mockery\MockInterface;

class TestControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $prof;
    protected User $student;
    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prof = User::factory()->prof()->create();
        $this->student = User::factory()->student()->create();
        $this->subject = Subject::factory()->create(['name' => 'Mathematics']); // Non-admin subject
    }

    /** @test */
    public function prof_can_view_own_tests()
    {
        Test::factory()->count(3)->create(['create_by' => $this->prof->id]);
        Test::factory()->count(2)->create();

        $response = $this->actingAs($this->prof)->get('/prof/tests');

        $response->assertStatus(200)
            ->assertViewIs('prof.tests.index')
            ->assertViewHas('tests', fn ($tests) => $tests->count() === 3);
    }

    /** @test */
    public function prof_index_shows_only_own_tests_with_question_count()
    {
        $ownTest1 = Test::factory()->create(['create_by' => $this->prof->id]);
        Question::factory()->count(3)->create(['test_id' => $ownTest1->id]);
        
        $ownTest2 = Test::factory()->create(['create_by' => $this->prof->id]);
        Question::factory()->count(1)->create(['test_id' => $ownTest2->id]);
        
        Test::factory()->create(); // Other prof

        $response = $this->actingAs($this->prof)->get('/prof/tests');

        $response->assertStatus(200)
            ->assertSee((string) $ownTest1->questions_count) // 3
            ->assertSee((string) $ownTest2->questions_count); // 1
    }

    /** @test */
    public function student_can_view_tests_for_class()
    {
        $test = Test::factory()->create(['subject_id' => $this->subject->id]);
        $this->student->update(['class_id' => $this->subject->class_room_id]); // Match class

        $response = $this->actingAs($this->student)->get('/student/tests');

        $response->assertStatus(200)
            ->assertViewHas('tests');
    }

    /** @test */
    public function prof_can_access_create_form()
    {
        $response = $this->actingAs($this->prof)->get('/prof/tests/create');

        $response->assertStatus(200)
            ->assertViewIs('prof.tests.create')
            ->assertViewHas('subjects') // Should exclude 'administration'
            ->assertSee($this->subject->name);
    }

    /** @test */
    public function student_cannot_access_prof_create()
    {
        $response = $this->actingAs($this->student)->get('/prof/tests/create');
        $response->assertStatus(403);
    }

    /** @test */
    public function prof_can_store_valid_test_with_manual_questions()
    {
        $questions = [
            [
                'question' => 'What is 2+2?',
                'answers' => [
                    ['answer' => '4', 'is_correct' => true],
                    ['answer' => '5', 'is_correct' => false],
                ]
            ]
        ];

        $response = $this->actingAs($this->prof)->post('/prof/tests', [
            'title' => 'Math Test',
            'subject_id' => $this->subject->id,
            'duration' => 60,
            'questions' => $questions,
        ]);

        $response->assertRedirect(route('prof.tests.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tests', [
            'title' => 'Math Test',
            'subject_id' => $this->subject->id,
            'duration' => 60,
            'create_by' => $this->prof->id
        ]);

        $this->assertDatabaseHas('questions', ['question' => 'What is 2+2?']);
        $this->assertDatabaseHas('answers', ['answer' => '4', 'is_correct' => 1]);
    }

    /** @test */
    public function prof_can_store_test_with_ai_file()
    {
        $qcmMock = Mockery::mock(QCMGenerator::class);
        $this->app->instance(QCMGenerator::class, $qcmMock);

        $fileMock = Mockery::mock('Illuminate\Http\UploadedFile');
        $qcmMock->shouldReceive('extractTextFromFile')->with($fileMock)->andReturn('Sample text');
        $qcmMock->shouldReceive('generateFromText')->andReturn([
            ['question' => 'AI Q1', 'answers' => [['answer' => 'AI A1', 'is_correct' => true]]]
        ]);
        $qcmMock->shouldReceive('saveToTest')->once();

        $response = $this->actingAs($this->prof)->post('/prof/tests', [
            'title' => 'AI Test',
            'subject_id' => $this->subject->id,
            'duration' => 30,
            'file' => $fileMock,
        ]);

        $response->assertRedirect(route('prof.tests.index'))
            ->assertSessionHas('success');

        $test = Test::where('title', 'AI Test')->first();
        $this->assertTrue($test->is_ai_generated);
    }

    /** @test */
    public function store_validates_required_fields()
    {
        $response = $this->actingAs($this->prof)->post('/prof/tests', []);

        $response->assertSessionHasErrors(['title', 'subject_id', 'duration']);
    }

    /** @test */
    public function store_validates_subject_exists()
    {
        $response = $this->actingAs($this->prof)->post('/prof/tests', [
            'title' => 'Invalid',
            'subject_id' => 9999,
            'duration' => 60,
        ]);

        $response->assertSessionHasErrors(['subject_id']);
    }

    /** @test */
    public function store_validates_questions_when_no_file()
    {
        $response = $this->actingAs($this->prof)->post('/prof/tests', [
            'title' => 'Test',
            'subject_id' => $this->subject->id,
            'duration' => 60,
            'questions' => [['question' => '', 'answers' => []]],
        ]);

        $response->assertSessionHasErrors(['questions.0.question', 'questions.0.answers']);
    }

    /** @test */
    public function prof_can_view_prof_show_own_test()
    {
        $test = Test::factory()->create(['create_by' => $this->prof->id]);
        Question::factory()->create(['test_id' => $test->id]);
        Result::factory()->create(['test_id' => $test->id]);

        $response = $this->actingAs($this->prof)->get("/prof/tests/{$test->id}");

        $response->assertStatus(200)
            ->assertViewIs('prof.tests.show')
            ->assertViewHas('test');
    }

    /** @test */
    public function prof_cannot_view_other_prof_test()
    {
        $otherProf = User::factory()->prof()->create();
        $test = Test::factory()->create(['create_by' => $otherProf->id]);

        $response = $this->actingAs($this->prof)->get("/prof/tests/{$test->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function prof_can_view_student_result()
    {
        $test = Test::factory()->create(['create_by' => $this->prof->id]);
        $result = Result::factory()->create(['test_id' => $test->id, 'user_id' => $this->student->id]);

        $response = $this->actingAs($this->prof)->get("/prof/tests/{$test->id}/student/{$this->student->id}");

        $response->assertStatus(200)
            ->assertViewIs('prof.tests.student_result')
            ->assertViewHasAll(['test', 'result']);
    }

    /** @test */
    public function prof_can_generate_ai_for_own_test()
    {
        $test = Test::factory()->create(['create_by' => $this->prof->id]);
        $qcmMock = Mockery::mock(QCMGenerator::class);
        $this->app->instance(QCMGenerator::class, $qcmMock);
        $fileMock = Mockery::mock('Illuminate\Http\UploadedFile');

        $qcmMock->shouldReceive('extractTextFromFile')->andReturn('text');
        $qcmMock->shouldReceive('generateFromText')->andReturn([['question' => 'AI Q']]);
        $qcmMock->shouldReceive('saveToTest')->once();

        $response = $this->actingAs($this->prof)->post("/prof/tests/{$test->id}/generate-ai", [
            'file' => $fileMock
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');
        $this->assertTrue(Test::find($test->id)->is_ai_generated);
    }

    /** @test */
    public function prof_can_edit_own_test()
    {
        $test = Test::factory()->create(['create_by' => $this->prof->id]);

        $response = $this->actingAs($this->prof)->get("/prof/tests/{$test->id}/edit");

        $response->assertStatus(200);
    }

    /** @test */
    public function prof_can_update_own_test()
    {
        $test = Test::factory()->create(['create_by' => $this->prof->id]);

        $response = $this->actingAs($this->prof)->put("/prof/tests/{$test->id}", [
            'title' => 'Updated Title',
            'subject_id' => $this->subject->id,
            'duration' => 90,
        ]);

        $response->assertRedirect(route('prof.tests.index'));
        $this->assertDatabaseHas('tests', ['id' => $test->id, 'title' => 'Updated Title']);
    }

    /** @test */
    public function prof_can_destroy_own_test()
    {
        $test = Test::factory()->create(['create_by' => $this->prof->id]);
        Question::factory()->create(['test_id' => $test->id]);

        $response = $this->actingAs($this->prof)->delete("/prof/tests/{$test->id}");

        $response->assertRedirect(route('prof.tests.index'));
        $this->assertDatabaseMissing('tests', ['id' => $test->id]);
    }

    /** @test */
    public function unauthenticated_redirected()
    {
        $test = Test::factory()->create();
        $this->get('/prof/tests')->assertRedirect('/login');
        $this->post('/prof/tests')->assertRedirect('/login');
        $this->get("/prof/tests/{$test->id}")->assertRedirect('/login');
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
?>

