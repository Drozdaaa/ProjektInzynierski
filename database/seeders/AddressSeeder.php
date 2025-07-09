<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
         Address::insert([
            'city' => 'Warszawa',
            'street' => 'Marszałkowska',
            'postal_code' => '00-001',
            'building_number' => 10,
        ]);
    }
}
