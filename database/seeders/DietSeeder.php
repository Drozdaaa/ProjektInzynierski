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
            [
                'name' => 'Keto',
                'description' => 'Dieta niskowęglowodanowa'
            ],
            [
                'name' => 'Wegetariańska',
                'description' => 'Bez mięsa'
            ],
            [
                'name' => 'Wegańska',
                'description' => 'Bez produktów odzwierzęcych'
            ],
            [
                'name' => 'Bezglutenowa',
                'description' => 'Bez glutenu'
            ],
        ]);
    }
}
