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
                'start_time' => '15:00:00',
                'end_time' => '23:00:00',
                'number_of_people' => 50,
                'description' => 'Urodziny Pawła',
                'status_id' => 1,
                'event_type_id' => 2,
                'room_id'=>1,
                'restaurant_id' => 1,
                'user_id' => 3,
                'manager_id' => 3,
            ],
        ]);
    }
}
