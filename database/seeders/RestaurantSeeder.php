<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        Restaurant::insert([
            [
                'name' => 'Restauracja Polska',
                'description' => 'Tradycyjna kuchnia polska',
                'user_id' => 3,
                'address_id' => 1,
            ],
        ]);
    }
}
