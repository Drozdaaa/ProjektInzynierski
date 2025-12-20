<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        Restaurant::insert([
            [
                'name' => 'Restauracja Polska',
                'description' => 'Tradycyjna kuchnia polska',
                'booking_regulations' => "1. Rezerwacja staje się wiążąca po wpłaceniu zadatku w wysokości 20% szacowanego kosztu imprezy w terminie do 3 dni roboczych od dokonania wstępnej rezerwacji.\n" .
                                         "2. Ostateczną liczbę gości należy potwierdzić najpóźniej na 7 dni przed terminem wydarzenia.\n" .
                                         "3. Anulowanie rezerwacji na mniej niż 14 dni przed terminem skutkuje przepadkiem zadatku.\n" .
                                         "4. Po zakończeniu wydarzenia sala jest sprzątana przez obsługę (czas techniczny wliczony w rezerwację).",
                'user_id' => 3,
                'address_id' => 1,
            ],
            [
                'name' => 'Qwe',
                'description' => 'Super kuchnia polska',
                'booking_regulations' => "Przykładowy regulamin restauracji",
                'user_id' => 5,
                'address_id' => 2,
            ],
        ]);
    }
}
