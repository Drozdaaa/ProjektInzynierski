<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->jobTitle(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'id' => 1,
            'name' => 'Administrator',
        ]);
    }

    public function client(): static
    {
        return $this->state(fn(array $attributes) => [
            'id' => 2,
            'name' => 'Klient',
        ]);
    }

    public function manager(): static
    {
        return $this->state(fn(array $attributes) => [
            'id' => 3,
            'name' => 'Manager',
        ]);
    }
}
