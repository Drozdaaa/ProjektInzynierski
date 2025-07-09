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
            ['name' => 'Zaplanowane'],
            ['name' => 'W trakcie'],
            ['name' => 'Zakończone'],
        ]);
    }
}
