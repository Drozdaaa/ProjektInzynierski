<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_login_successfully()
    {
        // 1. ODBUDOWA RÓL: To musi być wykonane fizycznie w kodzie,
        // zanim wywołamy User::factory().
        Role::firstOrCreate(['name' => 'Administrator'], ['id' => 1]);
        Role::firstOrCreate(['name' => 'Klient'], ['id' => 2]);
        Role::firstOrCreate(['name' => 'Manager'], ['id' => 3]);

        // 2. Tworzenie użytkownika
        $user = User::factory()->create([
            'email' => 'jan@kowalski.pl',
            'password' => Hash::make('tajnehaslo'),
            'role_id' => 2,
            'is_active' => true,
        ]);

        // 3. Test przeglądarkowy
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(route('login'))
                    ->assertSee('Zaloguj się')
                    // Używamy dynamicznych danych z obiektu $user
                    ->type('email', $user->email)
                    ->type('password', 'tajnehaslo')
                    ->press('Zaloguj się')
                    ->pause(500)
                    ->assertPathIs('/')
                    // Weryfikacja sukcesu (np. powitanie użytkownika)
                    ->assertSee('Lokale');
        });
    }
}
