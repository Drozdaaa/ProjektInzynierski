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
                'name' => 'Orzechy',
                'description' => 'Alergia na orzechy laskowe i włoskie',
                'menu_id' => 1,
            ]
        ]);
    }
}
