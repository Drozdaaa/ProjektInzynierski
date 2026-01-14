<?php

namespace Tests\Browser;

use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_register_and_sees_verification_notice()
    {
        $this->browse(function (Browser $browser) {
            Role::firstOrCreate(['id' => 2, 'name' => 'Klient']);
            Role::firstOrCreate(['id' => 3, 'name' => 'Menadżer']);

            $browser->visit('/auth/register')
                ->assertSee('Zarejestruj się')

                ->type('first_name', 'Jan')
                ->type('last_name', 'Kowalski')
                ->type('phone', '123456789')
                ->type('email', 'jan@test.pl')
                ->type('password', 'Haslo123!')
                ->type('password_confirmation', 'Haslo123!')

                ->select('role_id', '2')

                ->press('Zarejestruj się')

                ->waitForLocation('/email/verify')
                ->assertSee('Zweryfikuj swój adres email')

                ->assertAuthenticated();
        });

        $this->assertDatabaseHas('users', [
            'email' => 'jan@test.pl',
            'first_name' => 'Jan',
            'role_id' => 2,
            'email_verified_at' => null
        ]);
    }

}
