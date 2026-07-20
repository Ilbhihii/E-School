<?php

namespace Database\Factories;

use App\Models\Level;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class LevelFactory extends Factory
{
    protected $model = Level::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['1ère', '2ème', '3ème']),
            'description' => $this->faker->sentence,
            'subject_id' => Subject::factory(),
            'order' => $this->faker->numberBetween(1, 4),
        ];
    }
}
?>
