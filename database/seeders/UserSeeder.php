<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;




class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert(
            [
                [
                    'first_name' => 'Jan',
                    'last_name' => 'Kowalski',
                    'phone' => '123456789',
                    'email' => 'jan.kowalski@example.com',
                    'password' => Hash::make('haslo123'),
                    'role_id' => 1
                ],
                [
                    'first_name' => 'Piotr',
                    'last_name' => 'Kowalski',
                    'phone' => '123455789',
                    'email' => 'piotr.kowalski@example.com',
                    'password' => Hash::make('haslo123'),
                    'role_id' => 2
                ],
                [
                    'first_name' => 'Paweł',
                    'last_name' => 'Kowalski',
                    'phone' => '123455449',
                    'email' => 'pawel.kowalski@example.com',
                    'password' => Hash::make('haslo123'),
                    'role_id' => 3
                ],
                [
                    'first_name' => 'Admin',
                    'last_name' => 'Admin',
                    'phone' => '123459789',
                    'email' => 'admin@admin.com',
                    'password' => Hash::make('admin123'),
                    'role_id' => 1
                ],
                [
                    'first_name' => 'Manager',
                    'last_name' => 'Manager',
                    'phone' => '123456799',
                    'email' => 'manager@manager.com',
                    'password' => Hash::make('manager123'),
                    'role_id' => 3
                ],
            ]
        );
    }
}
