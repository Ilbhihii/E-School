<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'answer' => $this->faker->sentence(4),
            'is_correct' => $this->faker->boolean(25), // 25% correct
        ];
    }

    public function correct()
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => true,
        ]);
    }

    public function incorrect()
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => false,
        ]);
    }
}
?>

