<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Room;
use App\Models\Menu;
use App\Models\Dish;
use App\Models\Event;
use App\Models\Address;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $restaurant = new Restaurant([
            'name' => 'Test Restaurant',
            'description' => 'Test Description',
            'booking_regulations' => 'Rules',
            'image' => 'path/to/image.jpg',
            'address_id' => 1,
            'user_id' => 1
        ]);

        $this->assertEquals('Test Restaurant', $restaurant->name);
        $this->assertEquals('Test Description', $restaurant->description);
        $this->assertEquals('Rules', $restaurant->booking_regulations);
        $this->assertEquals('path/to/image.jpg', $restaurant->image);
        $this->assertEquals(1, $restaurant->address_id);
        $this->assertEquals(1, $restaurant->user_id);
    }

    /** @test */
    public function it_belongs_to_an_address()
    {
        $address = Address::factory()->create();
        $restaurant = Restaurant::factory()->create(['address_id' => $address->id]);

        $this->assertInstanceOf(Address::class, $restaurant->address);
        $this->assertEquals($address->id, $restaurant->address->id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $restaurant->user);
        $this->assertEquals($user->id, $restaurant->user->id);
    }

    /** @test */
    public function it_has_many_dishes()
    {
        $restaurant = Restaurant::factory()->create();
        $dish = Dish::factory()->create(['restaurant_id' => $restaurant->id]);

        $this->assertTrue($restaurant->dishes->contains($dish));
        $this->assertInstanceOf(Dish::class, $restaurant->dishes->first());
    }

    /** @test */
    public function it_has_many_events()
    {
        $restaurant = Restaurant::factory()->create();
        $event = Event::factory()->create(['restaurant_id' => $restaurant->id]);

        $this->assertTrue($restaurant->events->contains($event));
        $this->assertInstanceOf(Event::class, $restaurant->events->first());
    }

    /** @test */
    public function it_has_many_menus()
    {
        $restaurant = Restaurant::factory()->create();
        $menu = Menu::factory()->create(['restaurant_id' => $restaurant->id]);

        $this->assertTrue($restaurant->menus->contains($menu));
        $this->assertInstanceOf(Menu::class, $restaurant->menus->first());
    }

    /** @test */
    public function it_has_many_rooms()
    {
        $restaurant = Restaurant::factory()->create();
        $room = Room::factory()->create(['restaurant_id' => $restaurant->id]);

        $this->assertTrue($restaurant->rooms->contains($room));
        $this->assertInstanceOf(Room::class, $restaurant->rooms->first());
    }
}
