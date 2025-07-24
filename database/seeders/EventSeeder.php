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
                'number_of_people' => 50,
                'description' => 'Urodziny Pawła',
                'status_id' => 1,
                'event_type_id' => 2,
                'restaurant_id' => 1,
                'user_id' => 3,
                'menu_id' => 1,
                'manager_id' => 3,
            ],
        ]);
    }
}
