<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Allergy;

class AllergySeeder extends Seeder
{
    public function run(): void
    {
        Allergy::insert([
            [
                'name' => 'gluten',
                'description' => 'Zboża zawierające gluten: pszenica, żyto, jęczmień, owies'
            ],
            [
                'name' => 'skorupiaki',
                'description' => 'Krewetki, kraby, homary i inne skorupiaki'
            ],
            [
                'name' => 'jaja',
                'description' => 'Jaja kurze oraz produkty na ich bazie'
            ],
            [
                'name' => 'ryby',
                'description' => 'Ryby i produkty pochodzenia rybnego'
            ],
            [
                'name' => 'orzechy',
                'description' => 'Orzechy laskowe, włoskie, nerkowce, migdały i inne'
            ],
            [
                'name' => 'soja',
                'description' => 'Soja oraz produkty pochodne'
            ],
            [
                'name' => 'laktoza',
                'description' => 'Cukier mleczny i produkty mleczne'
            ],
            [
                'name' => 'seler',
                'description' => 'Seler oraz produkty zawierające seler'
            ],
            [
                'name' => 'gorczyca',
                'description' => 'Gorczyca oraz produkty na jej bazie'
            ],
            [
                'name' => 'nasiona sezamu',
                'description' => 'Sezam oraz produkty sezamowe'
            ],
            [
                'name' => 'dwutlenek siarki',
                'description' => 'Siarczyny i dwutlenek siarki'
            ],
            [
                'name' => 'łubin',
                'description' => 'Łubin oraz produkty pochodne'
            ],
            [
                'name' => 'mięczaki',
                'description' => 'Małże, ostrygi, ślimaki i inne mięczaki'
            ],
        ]);
    }
}
