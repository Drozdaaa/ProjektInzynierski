<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Event;
use App\Models\Menu;
use App\Models\User;
use App\Models\Room;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_casts_original_data_to_array()
    {
        $event = new Event();

        $data = ['foo' => 'bar', 'price' => 100];
        $event->original_data = $data;

        $this->assertIsArray($event->original_data);
        $this->assertEquals('bar', $event->original_data['foo']);
    }

    /** @test */
    public function it_belongs_to_many_menus()
    {
        $event = new Event();
        $relation = $event->menus();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Menu::class, $relation->getRelated());
    }

    /** @test */
    public function it_belongs_to_many_rooms()
    {
        $event = new Event();
        $relation = $event->rooms();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Room::class, $relation->getRelated());
    }

    /** @test */
    public function it_belongs_to_user_and_manager()
    {
        $event = new Event();

        $this->assertInstanceOf(BelongsTo::class, $event->user());
        $this->assertInstanceOf(User::class, $event->user()->getRelated());

        $this->assertInstanceOf(BelongsTo::class, $event->manager());
        $this->assertInstanceOf(User::class, $event->manager()->getRelated());
    }

    /** @test */
    public function it_belongs_to_restaurant()
    {
        $event = new Event();
        $this->assertInstanceOf(BelongsTo::class, $event->restaurant());
    }
}
