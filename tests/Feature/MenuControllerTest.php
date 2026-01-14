<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function unauthorized_user_cannot_create_a_menu()
    {
        /** @var User $user */
        $user = User::factory()->client()->create();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('menus.create', $restaurant->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_create_a_menu_successfully()
    {
        /** @var User $manager */
        $manager = User::factory()
            ->manager()
            ->create(['is_active' => true]);

        $restaurant = Restaurant::factory()->create([
            'user_id' => $manager->id,
        ]);

        $dishes = Dish::factory()
            ->count(3)
            ->create(['restaurant_id' => $restaurant->id]);

        $response = $this->actingAs($manager)
            ->post(route('menus.store', $restaurant->id), [
                'price' => 150.00,
                'dishes' => $dishes->pluck('id')->toArray(),
            ]);

        $response->assertRedirect(
            route('menus.index', ['restaurant' => $restaurant->id])
        );

        $response->assertSessionHas('success');
    }

    /** @test */
    public function unauthorized_user_cannot_delete_menu()
    {
        /** @var User $owner */
        $owner = User::factory()
            ->manager()
            ->create(['is_active' => true]);

        $restaurant = Restaurant::factory()->create([
            'user_id' => $owner->id,
        ]);

        $menu = Menu::factory()->create([
            'restaurant_id' => $restaurant->id,
        ]);

        /** @var User $intruder */
        $intruder = User::factory()
            ->manager()
            ->create(['is_active' => true]);

        $this->assertFalse($intruder->isAdmin());
        $this->assertNotEquals($owner->id, $intruder->id);

        $response = $this->actingAs($intruder)
            ->withoutMiddleware('manager.active')
            ->delete(route('menus.destroy', $menu->id));

        $response->assertStatus(403);

        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
        ]);
    }
}
