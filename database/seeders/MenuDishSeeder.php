<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuDishSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('menu_dish')->insert([
            ['menu_id' => 1, 'dish_id' => 1],
            ['menu_id' => 1, 'dish_id' => 2],
            ['menu_id' => 1, 'dish_id' => 3],
            ['menu_id' => 1, 'dish_id' => 4],
        ]);
    }
}
