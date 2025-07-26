<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        Status::insert([
            ['name' => 'Oczekujące'],
            ['name' => 'Zaplanowane'],
            ['name' => 'Zakończone'],
        ]);
    }
}
