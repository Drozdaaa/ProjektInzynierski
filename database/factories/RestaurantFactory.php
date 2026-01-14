<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Restaurant',
            'description' => $this->faker->paragraph(3),
            'booking_regulations' => $this->faker->text(200),
            'image' => null,

            'address_id' => Address::factory(),

            'user_id' => User::factory()->manager(),
        ];
    }
}
