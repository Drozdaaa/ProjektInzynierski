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
                'is_available' => true,
                'restaurant_id' => 1,
            ],

            [
                'name' => 'Sala kameralna',
                'capacity' => 40,
                'description' => 'Mniejsza sala z klimatyzacją.',
                'is_available' => true,
                'restaurant_id' => 1,
            ],

            [
                'name' => 'Sala Panorama',
                'capacity' => 80,
                'description' => 'Sala z dużymi oknami i widokiem na ogród.',
                'is_available' => true,
                'restaurant_id' => 2,

            ],

            [
                'name' => 'Sala Tarasowa',
                'capacity' => 60,
                'description' => 'Sala z wyjściem na taras i dostępem do ogrodu.',
                'is_available' => true,
                'restaurant_id' => 2,
            ],
        ]);
    }
}
