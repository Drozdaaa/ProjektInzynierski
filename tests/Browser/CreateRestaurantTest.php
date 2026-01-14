<?php

namespace Tests\Browser;

use App\Models\Role;
use App\Models\User;
use App\Models\Restaurant;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateRestaurantTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function manager_can_create_restaurant_and_add_rooms_successfully()
    {
        $this->browse(function (Browser $browser) {
            $managerRole = Role::firstOrCreate(['name' => 'Manager'], ['id' => 3]);
            Role::firstOrCreate(['name' => 'Klient'], ['id' => 2]);

            $manager = User::factory()->create([
                'role_id' => $managerRole->id,
                'email' => 'manager@test.pl',
                'password' => bcrypt('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $browser->loginAs($manager)
                ->visit(route('restaurants.create'))
                ->assertSee('Dodaj nową restaurację')

                ->type('name', 'Pizzeria Napoli')
                ->type('description', 'Najlepsza pizza w mieście.')
                ->type('booking_regulations', 'Rezerwacja ważna 15 min.')
                ->type('street', 'Mickiewicza')
                ->type('building_number', '10/2')
                ->type('city', 'Warszawa')
                ->type('postal_code', '00-001')
                ->press('Dodaj restaurację')

                ->waitForText('Restauracja została utworzona')
                ->assertPathIs('/restaurants/*/rooms/create')

                ->type('name', 'Sala Główna')
                ->type('capacity', '50')
                ->type('price', '100')
                ->type('cleaning_hours', '1')
                ->type('cleaning_minutes', '30')
                ->press('Dodaj salę')

                ->waitFor('.list-group')
                ->assertSee('Sala została dodana.')

                ->within('.list-group', function ($list) {
                    $list->assertSee('Sala Główna')
                         ->assertSee('50 osób');
                })

                ->type('name', 'Ogródek')
                ->type('capacity', '20')
                ->type('price', '1000')
                ->clickLink('Zakończ dodawanie')

                ->assertRouteIs('users.manager-dashboard');
        });
    }

    /** @test */
    public function user_cannot_create_second_restaurant()
    {
        $this->browse(function (Browser $browser) {
            $managerRole = Role::firstOrCreate(['name' => 'Manager'], ['id' => 3]);
            Role::firstOrCreate(['name' => 'Klient'], ['id' => 2]);

            $manager = User::factory()->create([
                'role_id' => $managerRole->id,
                'email_verified_at' => now(),
            ]);

            Restaurant::factory()->create([
                'user_id' => $manager->id,
                'description' => 'Krótki opis',
                'booking_regulations' => 'Krótki regulamin',
            ]);

            $browser->loginAs($manager)
                ->visit(route('restaurants.create'))
                ->assertRouteIs('restaurants.index')
                ->assertSee('Posiadasz już utworzoną restaurację.');
        });
    }
}
