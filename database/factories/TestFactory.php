<?php

namespace Database\Factories;

use App\Models\Test;
use App\Models\Course;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestFactory extends Factory
{
    protected $model = Test::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'subject_id' => Subject::factory(),
            'duration' => $this->faker->numberBetween(30, 120),
            'create_by' => User::factory()->create(['role' => 'prof'])->id,
        ];
    }
}
?>

