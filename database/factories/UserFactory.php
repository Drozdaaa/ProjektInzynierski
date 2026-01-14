<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->unique()->numerify('#########'),
            'email' => $this->faker->unique()->safeEmail(),
            'is_active' => true,
            'password' => Hash::make('password'),
            'role_id' => Role::firstOrCreate(['name' => 'Klient'])->id,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::where('name', 'Administrator')->first()->id,
        ]);
    }

    public function manager(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::where('name', 'Manager')->first()->id,
        ]);
    }

    public function client(): static
    {
        return $this->state(fn () => [
            'role_id' => Role::where('name', 'Klient')->first()->id,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
