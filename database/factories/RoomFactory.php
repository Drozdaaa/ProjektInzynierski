<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->word) . ' Sala',
            'capacity' => $this->faker->numberBetween(20, 200),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'cleaning_duration' => $this->faker->randomElement([30, 60, 90, 120]),

            'restaurant_id' => Restaurant::factory(),
        ];
    }
}
