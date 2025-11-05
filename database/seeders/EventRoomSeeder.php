<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventRoomSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('event_room')->insert([
            [
                'event_id' => 1,
                'room_id' => 1,
            ],

        ]);
    }
}

