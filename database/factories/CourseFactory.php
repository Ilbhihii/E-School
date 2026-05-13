<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'level_id' => null,
            'subject_id' => null,
            'user_id' => null,
            'admin_id' => null,
        ];
    }
}
