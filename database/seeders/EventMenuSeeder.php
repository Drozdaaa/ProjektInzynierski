<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventMenuSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('event_menu')->insert([
            [
                'event_id' => 1,
                'menu_id'  => 1,
            ],
        ]);
    }
}
