<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DishType;

class DishTypeSeeder extends Seeder
{
    public function run(): void
    {
        DishType::insert([
            ['name' => 'Przystawka'],
            ['name' => 'Zupa'],
            ['name' => 'Danie główne'],
            ['name' => 'Deser'],
        ]);
    }
}
