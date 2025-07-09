<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dish;

class DishSeeder extends Seeder
{
    public function run(): void
    {
        Dish::create([
            [
                'name' => 'Tatar',
                'description' => 'Tatar z mięsa wołowego',
                'price' => 30.50,
                'dish_type_id' => 1,
                'restaurant_id' => 1,
            ],
            [
                'name' => 'Rosół',
                'description' => 'Rosół z kaczki',
                'price' => 12.50,
                'dish_type_id' => 2,
                'restaurant_id' => 1,
            ],
            [
                'name' => 'Schabowy',
                'description' => 'Schabowy z ziemniakmi oraz bukietem surówek',
                'price' => 21.50,
                'dish_type_id' => 3,
                'restaurant_id' => 1,
            ],
            [
                'name' => 'Lody',
                'description' => 'Pucharek lodów z bitą śmietaną i posypką z orzechów laskowych',
                'price' => 8.50,
                'dish_type_id' => 4,
                'restaurant_id' => 1,
            ],
        ]);
    }
}
