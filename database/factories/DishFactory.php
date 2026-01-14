<?php

namespace Database\Factories;

use App\Models\DishType;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DishFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->words(rand(2, 3), true)),
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->randomFloat(2, 15, 99),
            'dish_type_id' => DishType::factory(),
            'restaurant_id' => Restaurant::factory(),
        ];
    }
}
