<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Test;
use App\Models\Course;
use App\Models\Question;
use App\Models\Result;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_uses_has_factory()
    {
        $test = Test::factory()->create();

        $this->assertInstanceOf(Test::class, $test);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $test = Test::factory()->create([
            'title' => 'Sample Test',
            'course_id' => Course::factory(),
            'duration' => 60,
            'create_by' => 1,
            'is_ai_generated' => true,
        ]);

        $this->assertEquals('Sample Test', $test->title);
        $this->assertEquals(60, $test->duration);
        $this->assertTrue($test->is_ai_generated);
    }

    /** @test */
    public function it_belongs_to_course()
    {
        $course = Course::factory()->create();
        $test = Test::factory()->create(['course_id' => $course->id]);

        $this->assertInstanceOf(Course::class, $test->course);
        $this->assertEquals($course->id, $test->course->id);
    }

    /** @test */
    public function it_has_many_questions()
    {
        $test = Test::factory()->create();
        Question::factory()->count(3)->create(['test_id' => $test->id]);
        Question::factory()->count(2)->create();

        $this->assertCount(3, $test->questions);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $test->questions);
        $this->assertEquals($test->id, $test->questions->first()->test_id);
    }

    /** @test */
    public function it_has_many_results()
    {
        $test = Test::factory()->create();
        Result::factory()->count(2)->create(['test_id' => $test->id]);
        Result::factory()->count(1)->create();

        $this->assertCount(2, $test->results);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $test->results);
        $this->assertEquals($test->id, $test->results->first()->test_id);
    }

    /** @test */
    public function course_relationship_returns_null_when_no_course()
    {
        $test = Test::factory()->create(['course_id' => null]);

        $this->assertNull($test->course);
    }

    /** @test */
    public function it_returns_empty_collection_when_no_questions()
    {
        $test = Test::factory()->create();

        $this->assertCount(0, $test->questions);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $test->questions);
    }

    /** @test */
    public function it_returns_empty_collection_when_no_results()
    {
        $test = Test::factory()->create();

        $this->assertCount(0, $test->results);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $test->results);
    }

    /** @test */
    public function factory_creates_test_with_relationships()
    {
        $test = Test::factory()->create();

        $this->assertNotNull($test->course_id);
        $this->assertNotNull($test->create_by);
        $this->assertInstanceOf(Course::class, $test->course);
    }
}
?>
