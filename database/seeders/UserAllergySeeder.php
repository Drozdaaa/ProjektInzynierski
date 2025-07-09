<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserAllergySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_allergy')->insert([
            'user_id' => 1,
            'allergy_id' => 1,
        ]);
    }
}
