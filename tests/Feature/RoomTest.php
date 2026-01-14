<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Room;
use App\Models\Role;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Tworzymy role, aby fabryki i Gate działały poprawnie
        Role::firstOrCreate(['id' => 1], ['name' => 'Administrator']);
        Role::firstOrCreate(['id' => 2], ['name' => 'Klient']);
        Role::firstOrCreate(['id' => 3], ['name' => 'Manager']);
    }

    /** @test */
    public function manager_can_add_room_to_own_restaurant()
    {
        $manager = User::factory()->manager()->create(['is_active' => true]);
        $restaurant = Restaurant::factory()->create(['user_id' => $manager->id]);

        $response = $this->actingAs($manager)
            ->post(route('rooms.store', $restaurant->id), [
                'name' => 'Sala Weselna',
                'capacity' => 100,
                'price' => 500.00,
                'description' => 'Duża sala',
                'cleaning_hours' => 1,
                'cleaning_minutes' => 30,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('rooms', [
            'name' => 'Sala Weselna',
            'restaurant_id' => $restaurant->id,
            'cleaning_duration' => 90,
        ]);
    }

    /** @test */
    public function manager_can_delete_own_room()
    {
        $manager = User::factory()->manager()->create(['is_active' => true]);
        $restaurant = Restaurant::factory()->create(['user_id' => $manager->id]);
        $room = Room::factory()->create(['restaurant_id' => $restaurant->id]);

        $response = $this->actingAs($manager)
            ->delete(route('rooms.destroy', ['restaurant' => $restaurant->id, 'room' => $room->id]));

        $response->assertRedirect(route('restaurants.index'));
        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }

    /** @test */
    public function regular_user_cannot_add_room()
    {
        $client = User::factory()->client()->create();
        $manager = User::factory()->manager()->create();
        $restaurant = Restaurant::factory()->create(['user_id' => $manager->id]);

        $response = $this->actingAs($client)
            ->post(route('rooms.store', $restaurant->id), [
                'name' => 'Nielegalna Sala',
                'capacity' => 50,
                'price' => 100,
                'cleaning_hours' => 1,
                'cleaning_minutes' => 0,
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('rooms', [
            'name' => 'Nielegalna Sala',
            'restaurant_id' => $restaurant->id
        ]);
    }

    /** @test */
    public function regular_user_cannot_delete_room()
    {
        $client = User::factory()->client()->create();

        $manager = User::factory()->manager()->create();
        $restaurant = Restaurant::factory()->create(['user_id' => $manager->id]);
        $room = Room::factory()->create(['restaurant_id' => $restaurant->id]);

        $response = $this->actingAs($client)
            ->delete(route('rooms.destroy', ['restaurant' => $restaurant->id, 'room' => $room->id]));

        $response->assertStatus(403);

        $this->assertDatabaseHas('rooms', ['id' => $room->id]);
    }

    /** @test */
    public function manager_cannot_delete_room_from_another_restaurant()
    {
        $manager1 = User::factory()->manager()->create();
        $restaurant1 = Restaurant::factory()->create(['user_id' => $manager1->id]);

        $intruder = User::factory()->manager()->create();

        $room = Room::factory()->create(['restaurant_id' => $restaurant1->id]);

        $response = $this->actingAs($intruder)
            ->delete(route('rooms.destroy', ['restaurant' => $restaurant1->id, 'room' => $room->id]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('rooms', ['id' => $room->id]);
    }
}
