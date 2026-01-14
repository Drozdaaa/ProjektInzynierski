<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Room;
use App\Models\EventType;
use App\Models\Status;
use App\Models\Menu;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_create_event_successfully()
    {
        $user = User::factory()->client()->create();
        $manager = User::factory()->manager()->create();
        $restaurant = Restaurant::factory()->create([
            'user_id' => $manager->id,
            'booking_regulations' => 'Przykładowy regulamin'
        ]);

        $room = Room::factory()->create(['restaurant_id' => $restaurant->id]);
        $menu = Menu::factory()->create(['restaurant_id' => $restaurant->id]);
        $eventType = EventType::factory()->create();
        Status::factory()->create(['id' => 1, 'name' => 'Oczekujące']);

        $date = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->actingAs($user)->post(route('events.store', $restaurant->id), [
            'start_date' => $date,
            'end_date' => $date,
            'event_type_id' => $eventType->id,
            'description' => 'Urodziny',
            'action' => 'default',

            'terms' => true,

            'hours' => [
                $date => ['start' => '14:00', 'end' => '18:00']
            ],
            'people' => [
                $date => 20
            ],
            'rooms' => [
                $date => [$room->id]
            ],
            'menus' => [
                $date => [$menu->id]
            ]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('events', [
            'date' => $date,
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
            'start_time' => '14:00',
        ]);
    }

    public function test_cannot_book_room_if_it_is_already_busy()
    {
        $user = User::factory()->client()->create();
        $manager = User::factory()->manager()->create();
        $restaurant = Restaurant::factory()->create(['user_id' => $manager->id]);
        $room = Room::factory()->create(['restaurant_id' => $restaurant->id]);
        $eventType = EventType::factory()->create();
        $status = Status::factory()->create(['id' => 1]);

        $date = Carbon::tomorrow()->format('Y-m-d');

        Event::factory()->create([
            'restaurant_id' => $restaurant->id,
            'date' => $date,
            'start_time' => '12:00',
            'end_time' => '16:00',
            'status_id' => $status->id,
            'event_type_id' => $eventType->id
        ])->rooms()->attach($room->id);

        $response = $this->actingAs($user)->post(route('events.store', $restaurant->id), [
            'start_date' => $date,
            'end_date' => $date,
            'event_type_id' => $eventType->id,
            'description' => 'Próba kolizji',
            'terms' => true,

            'hours' => [
                $date => ['start' => '13:00', 'end' => '15:00']
            ],
            'people' => [$date => 10],
            'rooms' => [$date => [$room->id]]
        ]);

        $this->assertCount(1, Event::all());
    }

    public function test_cleaning_duration_blocks_reservation()
    {
        $user = User::factory()->client()->create();
        $restaurant = Restaurant::factory()->create();
        $room = Room::factory()->create([
            'restaurant_id' => $restaurant->id,
            'cleaning_duration' => 60
        ]);
        $status = Status::factory()->create(['id' => 1]);
        $eventType = EventType::factory()->create();

        $date = Carbon::tomorrow()->addDay()->format('Y-m-d');

        Event::factory()->create([
            'restaurant_id' => $restaurant->id,
            'date' => $date,
            'start_time' => '10:00',
            'end_time' => '14:00',
            'status_id' => $status->id,
            'event_type_id' => $eventType->id,
        ])->rooms()->attach($room->id);

        $response = $this->actingAs($user)->post(route('events.store', $restaurant->id), [
            'start_date' => $date,
            'end_date' => $date,
            'event_type_id' => $eventType->id,
            'description' => 'Test',
            'terms' => true,

            'hours' => [
                $date => ['start' => '14:30', 'end' => '16:00']
            ],
            'people' => [$date => 10],
            'rooms' => [$date => [$room->id]]
        ]);

        $response->assertSessionHas('error');
    }


    public function test_calendar_endpoint_returns_correct_json()
    {
        $manager = User::factory()->manager()->create();
        $restaurant = Restaurant::factory()->create(['user_id' => $manager->id]);
        $room = Room::factory()->create(['restaurant_id' => $restaurant->id, 'name' => 'Sala VIP']);

        $event = Event::factory()->create([
            'restaurant_id' => $restaurant->id,
            'date' => '2024-12-24',
            'start_time' => '18:00',
            'end_time' => '22:00'
        ]);
        $event->rooms()->attach($room->id);

        $response = $this->getJson(route('events.calendar', $restaurant->id));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Sala VIP (18:00 - 22:00)',
                'start' => '2024-12-24T18:00',
            ]);
    }

    /** @test */
    public function test_client_cannot_create_event_in_the_past()
    {
        $user = User::factory()->client()->create();
        $manager = User::factory()->manager()->create();
        $restaurant = Restaurant::factory()->create(['user_id' => $manager->id]);

        $room = Room::factory()->create(['restaurant_id' => $restaurant->id]);
        $eventType = EventType::factory()->create();
        Status::factory()->create(['id' => 1]);

        $pastDate = Carbon::yesterday()->format('Y-m-d');
        $response = $this->actingAs($user)->post(route('events.store', $restaurant->id), [
            'start_date' => $pastDate,
            'end_date' => $pastDate,
            'event_type_id' => $eventType->id,
            'description' => 'Próba rezerwacji wstecznej',
            'terms' => true,

            'hours' => [
                $pastDate => ['start' => '12:00', 'end' => '14:00']
            ],
            'people' => [$pastDate => 10],
            'rooms' => [$pastDate => [$room->id]]
        ]);

        $response->assertSessionHasErrors(['start_date']);

        $this->assertDatabaseCount('events', 0);
    }
}
