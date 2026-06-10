<?php

namespace Database\Factories;

use App\Models\Result;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResultFactory extends Factory
{
    protected $model = Result::class;

    public function definition(): array
    {
        return [
            'test_id' => Test::factory(),
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'score' => $this->faker->numberBetween(0, 10),
        ];
    }
}
?>

