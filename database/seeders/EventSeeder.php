<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::insert([
            [
                'date' => '2025-08-15',
                'reservation_id' => '018fba39-8e90-7e8e-a1b2-3c4d5e6f7a8b',
                'start_time' => '15:00:00',
                'end_time' => '23:00:00',
                'number_of_people' => 50,
                'description' => 'Urodziny Pawła',
                'status_id' => 1,
                'event_type_id' => 2,
                'restaurant_id' => 1,
                'user_id' => 3,
                'manager_id' => 3,
            ],
        ]);
    }
}
