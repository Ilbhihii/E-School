<?php

namespace Database\Factories;

use App\Models\ClassRoom;
use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassRoomFactory extends Factory
{
    protected $model = ClassRoom::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word . ' ' . $this->faker->numberBetween(1, 12),
            'level_id' => Level::factory(),
        ];
    }
}
?>

