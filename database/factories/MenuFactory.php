<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    public function definition(): array
    {
        return [
            'price' => $this->faker->randomFloat(2, 50, 300),
            'user_id' => User::factory(),
            'restaurant_id' => Restaurant::factory(),
        ];
    }
}
