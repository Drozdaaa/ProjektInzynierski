<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Diet;

class DietSeeder extends Seeder
{
    public function run(): void
    {
        Diet::insert([
            [
                'name' => 'keto',
                'description' => 'Dieta niskowęglowodanowa, oparta na wysokim spożyciu tłuszczów'
            ],
            [
                'name' => 'wegetariańska',
                'description' => 'Dieta bez mięsa, oparta na produktach roślinnych i nabiale'
            ],
            [
                'name' => 'wegańska',
                'description' => 'Dieta bez produktów odzwierzęcych, oparta wyłącznie na roślinach'
            ],
            [
                'name' => 'bezglutenowa',
                'description' => 'Dieta eliminująca gluten, zalecana przy celiakii i nietolerancjach'
            ],
            [
                'name' => 'śródziemnomorska',
                'description' => 'Zbilansowana dieta oparta na warzywach, rybach, oliwie i pełnych ziarnach'
            ],
            [
                'name' => 'niskotłuszczowa',
                'description' => 'Dieta ograniczająca tłuszcze, stosowana m.in. przy chorobach układu pokarmowego'
            ],
            [
                'name' => 'kopenhaska',
                'description' => 'Bardzo niskokaloryczna dieta krótkoterminowa (600-800 kcal)'
            ],
            [
                'name' => 'dukana',
                'description' => 'Dieta wysokobiałkowa, nastawiona na szybką redukcję masy ciała'
            ],
        ]);
    }
}
