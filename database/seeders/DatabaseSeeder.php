<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            AddressSeeder::class,
            DishTypeSeeder::class,
            EventTypeSeeder::class,
            RestaurantSeeder::class,
            DishSeeder::class,
            MenuSeeder::class,
            StatusSeeder::class,
            EventSeeder::class,
            AllergySeeder::class,
            DietSeeder::class,
            MenuDishSeeder::class,
            UserAllergySeeder::class,
        ]);
    }
}
