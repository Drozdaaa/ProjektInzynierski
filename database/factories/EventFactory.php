<?php

namespace Database\Factories;

use App\Models\EventType;
use App\Models\Restaurant;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('+1 week', '+1 year')->format('Y-m-d');

        return [
            'reservation_id' => (string) Str::uuid(),
            'date' => $date,
            'start_time' => '14:00:00',
            'end_time' => '22:00:00',
            'number_of_people' => $this->faker->numberBetween(10, 150),
            'description' => $this->faker->sentence(),
            'original_data' => null,
            'status_id' => Status::factory(),
            'event_type_id' => EventType::factory(),
            'restaurant_id' => Restaurant::factory(),

            'user_id' => User::factory(),

            'manager_id' => User::factory(),
        ];
    }
}
