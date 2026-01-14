<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventTypeFactory extends Factory
{
    public function definition(): array
    {

        return [
            'name' => $this->faker->unique()->randomElement([
                'Wesele',
                'Urodziny',
                'Chrzciny',
                'Spotkanie firmowe',
                'Stypa',
                'Inne'
            ]),
        ];
    }
}
