<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Menu;
use App\Models\User;
use App\Models\Dish;
use App\Models\Event;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $menu = new Menu([
            'price' => 150.00,
            'user_id' => 1,
        ]);

        $this->assertEquals(150.00, $menu->price);
        $this->assertEquals(1, $menu->user_id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $menu = Menu::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $menu->user);
        $this->assertEquals($user->id, $menu->user->id);
    }

    /** @test */
    public function it_belongs_to_a_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $menu = Menu::factory()->create(['restaurant_id' => $restaurant->id]);

        $this->assertInstanceOf(Restaurant::class, $menu->restaurant);
        $this->assertEquals($restaurant->id, $menu->restaurant->id);
    }

    /** @test */
    public function it_belongs_to_many_dishes()
    {
        $menu = Menu::factory()->create();
        $dish = Dish::factory()->create();

        $menu->dishes()->attach($dish);

        $this->assertTrue($menu->dishes->contains($dish));
        $this->assertInstanceOf(Dish::class, $menu->dishes->first());
    }

    /** @test */
    public function it_belongs_to_many_events()
    {
        $menu = Menu::factory()->create();
        $event = Event::factory()->create();

        $menu->events()->attach($event);

        $this->assertTrue($menu->events->contains($event));
        $this->assertInstanceOf(Event::class, $menu->events->first());
    }

    /** @test */
    public function it_checks_if_menu_is_shared()
    {
        $menuShared = Menu::factory()->create();

        $this->assertTrue($menuShared->isShared());

        $menuMultiEvent = Menu::factory()->create();
        $events = Event::factory()->count(2)->create();
        $menuMultiEvent->events()->attach($events);

        $this->assertTrue($menuMultiEvent->isShared());

        $menuSingle = Menu::factory()->create();
        $event = Event::factory()->create();

        $menuSingle->events()->attach($event);

        $menuSingle->event_id = $event->id;

        $this->assertFalse($menuSingle->isShared());
    }
}
