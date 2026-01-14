<?php

namespace Tests\Browser;

use Carbon\Carbon;
use App\Models\Dish;
use App\Models\Menu;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use App\Models\Event;
use App\Models\Status;
use Tests\DuskTestCase;
use App\Models\EventType;
use Laravel\Dusk\Browser;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateEventTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_client_can_create_event()
    {
        $this->browse(function (Browser $browser) {
            Role::firstOrCreate(['name' => 'Administrator'], ['id' => 1]);
            Role::firstOrCreate(['name' => 'Klient'], ['id' => 2]);
            Role::firstOrCreate(['name' => 'Manager'], ['id' => 3]);

            $user = User::factory()->client()->create();

            $restaurant = Restaurant::factory()->create([
                'booking_regulations' => 'Akceptuj nasz regulamin, aby zarezerwować.'
            ]);

            $room = Room::factory()->create(['restaurant_id' => $restaurant->id]);
            $menu = Menu::factory()->create(['restaurant_id' => $restaurant->id]);
            $dish = Dish::factory()->create(['restaurant_id' => $restaurant->id]);
            $menu->dishes()->attach($dish->id);

            $eventType = EventType::factory()->create(['name' => 'Wesele']);
            Status::firstOrCreate(['id' => 1], ['name' => 'Oczekujące']);

            $date = Carbon::tomorrow()->format('Y-m-d');

            $browser->loginAs($user)
                    ->visit(route('events.create', $restaurant->id))
                    ->resize(1920, 1080)
                    ->assertSee('Dodaj nowe wydarzenie')
                    ->waitFor('#bookingRegulationsModal', 10)
                    ->pause(500)
                    ->press('#acceptRegulationsBtn')
                    ->waitUntilMissing('#bookingRegulationsModal', 10)
                    ->script([
                        "document.getElementById('start_date').value = '$date';",
                        "document.getElementById('start_date').dispatchEvent(new Event('input'));",
                        "document.getElementById('start_date').dispatchEvent(new Event('change'));",
                        "document.getElementById('end_date').value = '$date';",
                        "document.getElementById('end_date').dispatchEvent(new Event('input'));",
                        "document.getElementById('end_date').dispatchEvent(new Event('change'));",
                    ]);

            $browser->pause(1500)
                    ->waitFor('.daily-block', 10)
                    ->assertSee("Konfiguracja na dzień")
                    ->waitFor('.daily-people-input', 5)
                    ->scrollIntoView('.daily-people-input')
                    ->pause(200)
                    ->type('.daily-people-input', 50);

            $roomSelector = ".room-checkbox[value='{$room->id}']";
            $browser->waitFor($roomSelector, 5)
                    ->scrollIntoView($roomSelector)
                    ->check($roomSelector);

            $menuSelector = "input[type='checkbox'][value='{$menu->id}']";
            $browser->waitFor($menuSelector, 5)
                    ->scrollIntoView($menuSelector)
                    ->check($menuSelector);

            $browser->select('event_type_id', $eventType->id)
                    ->type('description', 'Testowe wesele przez Dusk')
                    ->scrollIntoView('button[value="event"]')
                    ->pause(200)
                    ->press('Utwórz wydarzenie');

            $createdEvent = Event::first();

            $browser->waitForLocation("/restaurants/{$restaurant->id}/events/" . $createdEvent->id, 15)
                    ->assertSee('Rezerwacja została utworzona');
        });
    }
}
