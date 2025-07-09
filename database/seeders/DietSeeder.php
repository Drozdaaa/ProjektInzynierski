<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Diet;

class DietSeeder extends Seeder
{
    public function run(): void
    {
        Diet::insert([
            'name' => 'Wegetariańska',
            'description' => 'Dieta bez mięsa',
            'menu_id' => 1,
            'dish_id' => 4,
        ]);
    }
}
