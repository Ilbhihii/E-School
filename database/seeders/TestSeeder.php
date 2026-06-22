<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Test;
use App\Models\Course;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Level;
use App\Models\ClassRoom;
use App\Models\Subject;
use Faker\Factory as Faker;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Create a level (LevelSeeder already runs first and creates real levels)
        $level = Level::factory()->create();

        $classroom = ClassRoom::factory()->create([
            'level_id' => $level->id
        ]);

        // Create subject (class_id column was dropped from subjects, use pivot instead)
        $subject = Subject::factory()->create();
        // Link subject to classroom via pivot table
        $classroom->subjects()->attach($subject->id);

        $prof = User::factory()->prof()->create([
            'class_id' => $classroom->id
        ]);

        $course = Course::factory()->create([
            'class_id' => $classroom->id,
            'subject_id' => $subject->id,
            'user_id' => $prof->id,
            'admin_id' => $prof->id,
        ]);

        // course_id was renamed to subject_id in the tests table
        $test = Test::factory()->create([
            'subject_id' => $subject->id,
            'create_by' => $prof->id,
        ]);

        $question = Question::factory()->create([
            'test_id' => $test->id
        ]);

        Answer::factory()->correct()->create([
            'question_id' => $question->id
        ]);

        Answer::factory()->count(3)->incorrect()->create([
            'question_id' => $question->id
        ]);
    }

}
?>

