<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Dish;
use App\Models\Menu;
use App\Models\Diet;
use App\Models\Allergy;
use App\Models\DishType;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DishTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_dish_type()
    {
        $dishType = DishType::factory()->create();
        $dish = Dish::factory()->create([
            'dish_type_id' => $dishType->id
        ]);

        $this->assertInstanceOf(DishType::class, $dish->dishType);
        $this->assertEquals($dishType->id, $dish->dishType->id);
    }

    /** @test */
    public function it_belongs_to_a_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $dish = Dish::factory()->create([
            'restaurant_id' => $restaurant->id
        ]);

        $this->assertInstanceOf(Restaurant::class, $dish->restaurant);
        $this->assertEquals($restaurant->id, $dish->restaurant->id);
    }

    /** @test */
    public function it_belongs_to_many_menus()
    {
        $dish = Dish::factory()->create();
        $menu = Menu::factory()->create();
        $dish->menus()->attach($menu);

        $this->assertTrue($dish->menus->contains($menu));
        $this->assertInstanceOf(Menu::class, $dish->menus->first());
    }

    /** @test */
    public function it_belongs_to_many_diets()
    {
        $dish = Dish::factory()->create();
        $diet = Diet::factory()->create();
        $dish->diets()->attach($diet);

        $this->assertTrue($dish->diets->contains($diet));
        $this->assertInstanceOf(Diet::class, $dish->diets->first());
    }

    /** @test */
    public function it_belongs_to_many_allergies()
    {
        $dish = Dish::factory()->create();
        $allergy = Allergy::factory()->create();
        $dish->allergies()->attach($allergy);

        $this->assertTrue($dish->allergies->contains($allergy));
        $this->assertInstanceOf(Allergy::class, $dish->allergies->first());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $dish = new Dish([
            'name' => 'Pierogi',
            'description' => 'Pyszne z mięsem',
            'price' => 25.00,
            'dish_type_id' => 1,
            'restaurant_id' => 1
        ]);

        $this->assertEquals('Pierogi', $dish->name);
        $this->assertEquals('Pyszne z mięsem', $dish->description);
        $this->assertEquals(25.00, $dish->price);
        $this->assertEquals(1, $dish->dish_type_id);
        $this->assertEquals(1, $dish->restaurant_id);
    }
}
