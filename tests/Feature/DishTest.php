<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Dish;
use App\Models\User;
use App\Models\DishType;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DishTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function manager_can_create_a_dish()
    {
        /** @var User $user */
        $user = User::factory()
            ->manager()
            ->create(['is_active' => true]);

        $restaurant = Restaurant::factory()->create([
            'user_id' => $user->id,
        ]);

        $dishType = DishType::factory()->create();

        $response = $this->actingAs($user)->post(
            route('dishes.store', $restaurant->id),
            [
                'name' => 'Schabowy',
                'price' => 45.00,
                'description' => 'Pyszny kotlet',
                'dish_type_id' => $dishType->id,
            ]
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('dishes', [
            'name' => 'Schabowy',
            'price' => 45.00,
            'restaurant_id' => $restaurant->id,
        ]);
    }

    /** @test */
    public function manager_cannot_create_dish_in_another_restaurant()
    {
        /** @var User $intruder */
        $intruder = User::factory()->create([
            'role_id' => 3, // Manager
            'is_active' => true
        ]);

        $owner = User::factory()->create(['role_id' => 3]);
        $restaurant = Restaurant::factory()->create([
            'user_id' => $owner->id,
        ]);

        $dishType = DishType::factory()->create();

        $response = $this->actingAs($intruder)
            ->withoutMiddleware('manager.active')
            ->post(
                route('dishes.store', $restaurant->id),
                [
                    'name' => 'Nielegalne Danie',
                    'price' => 100.00,
                    'description' => 'To nie powinno przejść',
                    'dish_type_id' => $dishType->id,
                ]
            );
        $response->assertStatus(404);

        $this->assertDatabaseMissing('dishes', [
            'name' => 'Nielegalne Danie',
            'restaurant_id' => $restaurant->id,
        ]);
    }

     public function regular_user_cannot_delete_a_dish()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->client()->create();

        $manager = User::factory()->manager()->create();
        $restaurant = Restaurant::factory()->create(['user_id' => $manager->id]);
        $dishType = DishType::factory()->create();

        $dish = Dish::factory()->create([
            'restaurant_id' => $restaurant->id,
            'dish_type_id' => $dishType->id
        ]);
        $this->actingAs($user);

        $response = $this->delete(route('dishes.destroy', $dish->id));

        $response->assertStatus(403);

        $this->assertDatabaseHas('dishes', ['id' => $dish->id]);
    }
}
