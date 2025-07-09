<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EventType;

class EventTypeSeeder extends Seeder
{

    public function run(): void
    {
        EventType::insert([
           ['name'=>'Wesele'],
           ['name'=>'Urodziny'],
           ['name'=>'Chrzciny'],
           ['name'=>'Spotkanie firmowe'],
           ['name'=>'Stypa'],
        ]);
    }
}
