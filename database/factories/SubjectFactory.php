<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\ClassRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'type' => $this->faker->randomElement(['religieux', 'scolaire']),
            'class_id' => ClassRoom::factory(),
        ];
    }
}
?>

