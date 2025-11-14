<?php

namespace Database\Seeders;

use App\Models\Room;
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
            StatusSeeder::class,
            RestaurantSeeder::class,
            RoomSeeder::class,
            EventSeeder::class,
            DishSeeder::class,
            MenuSeeder::class,
            AllergySeeder::class,
            DietSeeder::class,
            MenuDishSeeder::class,
            EventRoomSeeder::class,
            EventMenuSeeder::class,
        ]);
    }
}
