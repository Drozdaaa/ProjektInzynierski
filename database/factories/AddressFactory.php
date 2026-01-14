<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city' => $this->faker->city(),
            'street' => $this->faker->streetName(),
            'building_number' => $this->faker->buildingNumber(),
            'postal_code' => $this->faker->regexify('[0-9]{2}-[0-9]{3}'),
        ];
    }
}
