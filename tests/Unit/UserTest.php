<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Menu;
use App\Models\Event;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = new User([
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'phone' => '123456789',
            'email' => 'jan@example.com',
            'is_active' => true,
            'password' => 'secret',
            'role_id' => 2,
        ]);

        $this->assertEquals('Jan', $user->first_name);
        $this->assertEquals('Kowalski', $user->last_name);
        $this->assertEquals('123456789', $user->phone);
        $this->assertEquals('jan@example.com', $user->email);
        $this->assertTrue($user->is_active);
        $this->assertEquals('secret', $user->password);
        $this->assertEquals(2, $user->role_id);
    }

    /** @test */
    public function it_belongs_to_a_role()
    {
        $role = Role::factory()->create();
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->assertInstanceOf(Role::class, $user->role);
        $this->assertEquals($role->id, $user->role->id);
    }

    /** @test */
    public function it_has_many_menus()
    {
        $user = User::factory()->create();
        $menu = Menu::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->menus->contains($menu));
        $this->assertInstanceOf(Menu::class, $user->menus->first());
    }

    /** @test */
    public function it_has_many_events()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->events->contains($event));
        $this->assertInstanceOf(Event::class, $user->events->first());
    }

    /** @test */
    public function it_has_one_restaurant()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Restaurant::class, $user->restaurants); 
        $this->assertEquals($restaurant->id, $user->restaurants->id);
    }

    /** @test */
    public function it_has_many_managed_events()
    {
        $manager = User::factory()->create(['role_id' => 3]);
        $event = Event::factory()->create(['manager_id' => $manager->id]);

        $this->assertTrue($manager->managedEvents->contains($event));
        $this->assertInstanceOf(Event::class, $manager->managedEvents->first());
    }

    /** @test */
    public function it_checks_user_roles_correctly()
    {
        $admin = new User(['role_id' => 1]);
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isUser());
        $this->assertFalse($admin->isManager());

        $user = new User(['role_id' => 2]);
        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->isUser());
        $this->assertFalse($user->isManager());

        $manager = new User(['role_id' => 3]);
        $this->assertFalse($manager->isAdmin());
        $this->assertFalse($manager->isUser());
        $this->assertTrue($manager->isManager());
    }
}
