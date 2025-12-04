<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::insert([
            [
                'name' => 'Sala Główna',
                'capacity' => 120,
                'description' => 'Duża sala bankietowa z parkietem i sceną.',
                'price' => 5000,
                'restaurant_id' => 1,
            ],

            [
                'name' => 'Sala kameralna',
                'capacity' => 40,
                'description' => 'Mniejsza sala z klimatyzacją.',
                'price' => 1500,
                'restaurant_id' => 1,
            ],

            [
                'name' => 'Sala Panorama',
                'capacity' => 80,
                'description' => 'Sala z dużymi oknami i widokiem na ogród.',
                'price' => 2500,
                'restaurant_id' => 2,

            ],

            [
                'name' => 'Sala Tarasowa',
                'capacity' => 60,
                'description' => 'Sala z wyjściem na taras i dostępem do ogrodu.',
                'price' => 1500,
                'restaurant_id' => 2,
            ],
        ]);
    }
}
