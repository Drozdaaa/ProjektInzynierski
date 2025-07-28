<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Allergy;

class AllergySeeder extends Seeder
{
    public function run(): void
    {
        Allergy::insert([
            [
                'name' => 'Gluten',
                'description' => 'Uczulenie na zboża: pszenica, żyto, jęczmień'
            ],
            [
                'name' => 'Orzechy',
                'description' => 'Uczulenie na: orzechy laskowe, włoskie, migdały'
            ],
            [
                'name' => 'Laktoza',
                'description' => 'Uczulenie na cukier mleczny'
            ],

        ]);
    }
}
