<?php

namespace Database\Factories;

use App\Models\Live;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LiveFactory extends Factory
{
    protected $model = Live::class;

    public function definition(): array
    {
        return [
            'title'      => $this->faker->sentence(3),
            'class_id'   => ClassRoom::factory(),
            'admin_id'   => User::factory(),
            'stream_url' => $this->faker->url(),
            'provider'   => $this->faker->randomElement(['youtube', 'teams', 'zoom']),
            'live_date'  => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time'   => '11:00:00',
        ];
    }
}
