<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationSecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_cannot_register_with_admin_role_id()
    {
        Role::create(['id' => 1, 'name' => 'Admin']);

        $payload = [
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'jan@example.com',
            'phone' => '123456789',
            'password' => 'Secret123',
            'password_confirmation' => 'Secret123',
            'role_id' => 1,
        ];

        $response = $this->post(route('register'), $payload);
        $response->assertSessionHasErrors(['role_id']);
        $response->assertSessionHasErrors([
            'role_id' => 'Nie można przypisać roli administratora.'
        ]);

        $this->assertDatabaseCount('users', 0);
    }

    /** @test */
    public function user_can_register_with_allowed_role_id()
    {
        Role::create(['id' => 1, 'name' => 'Admin']);
        Role::create(['id' => 2, 'name' => 'Klient']);

        $payload = [
            'first_name' => 'Anna',
            'last_name' => 'Nowak',
            'email' => 'anna@example.com',
            'phone' => '987654321',
            'password' => 'Secret123',
            'password_confirmation' => 'Secret123',
            'role_id' => 2,
        ];

        $response = $this->post(route('register'), $payload);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('verification.notice'));

        $this->assertDatabaseHas('users', [
            'email' => 'anna@example.com',
            'role_id' => 2
        ]);
    }
}
