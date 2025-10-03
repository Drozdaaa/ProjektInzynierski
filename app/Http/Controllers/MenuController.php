<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use App\Models\Event;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $restaurant = Restaurant::where('user_id', Auth::id())
            ->with('menus.dishes.dishType', 'menus.dishes.diets', 'menus.dishes.allergies')
            ->firstOrFail();

        $restaurant->menus->transform(function ($menu) {
            $menu->dishesByType = $menu->dishes->groupBy(fn($dish) => $dish->dishType->name);
            return $menu;
        });

        return view('menus.index', compact('restaurant'));
    }

    /**
     * Show the form for creating a new menu.
     */
    public function create(Restaurant $restaurant, Request $request)
    {
        if (Gate::denies('create-custom-menu')) {
            abort(403);
        }

        $dishes = $restaurant->dishes()->with(['dishType', 'diets', 'allergies'])->get();

        $fromEvent = $request->input('from_event', false);
        $eventId = $request->input('event_id');

        return view('menus.create', compact('restaurant', 'dishes', 'fromEvent', 'eventId'));
    }

    public function store(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'dishes' => 'required|array|min:1',
            'dishes.*' => 'exists:dishes,id',
        ]);

        $menu = $restaurant->menus()->create([
            'price' => $request->price,
            'user_id' => Auth::id(),
        ]);

        $menu->dishes()->sync($request->input('dishes'));

        if ($request->input('from_event')) {
            return redirect()->route('events.edit', [
                'id' => $request->input('event_id')
            ])->with('success', 'Menu zostało utworzone. Możesz teraz dokończyć rezerwację wydarzenia.');
        }

        return redirect()->route('menus.index', ['restaurant' => $restaurant->id])
            ->with('success', 'Menu zostało utworzone.');
    }


    /**
     * Display a menu assigned to event.
     */
    public function show($id)
    {
        $event = Event::with('menu.dishes.dishType')->findOrFail($id);

        return view('menus.show', [
            'event' => $event,
            'menu' => $event->menu,
            'dishes' => $event->menu->dishes,
        ]);
    }

    /**
     * Show the form for editing the specified menu.
     */
    public function edit($id)
    {
        $menu = Menu::findOrFail($id);

        if (Gate::denies('restaurant-owner', $menu->restaurant)) {
            abort(403);
        }

        $restaurant = $menu->restaurant()->with('dishes')->first();
        $selectedDishes = $menu->dishes->pluck('id')->toArray();
        $dishes = $restaurant->dishes()->with('dishType')->get();

        return view('menus.edit', compact('menu', 'restaurant', 'dishes', 'selectedDishes'));
    }

    /**
     * Update the specified menu in storage.
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        if (Gate::denies('restaurant-owner', $menu->restaurant)) {
            abort(403);
        }

        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'dishes' => 'array',
            'dishes.*' => 'exists:dishes,id',
        ]);

        $menu->price = $validated['price'];
        $menu->save();
        $menu->dishes()->sync($validated['dishes'] ?? []);

        return redirect()->route('menus.index')
            ->with('success', 'Menu zostało zaktualizowane.');
    }

    /**
     * Remove the specified menu from storage.
     */
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        if (Gate::denies('restaurant-owner', $menu->restaurant)) {
            abort(403);
        }

        $menu->delete();

        return redirect()
            ->route('menus.index', ['id' => $menu->restaurant_id])
            ->with('success', 'Menu zostało usunięte.');
    }

    public function createForUser(Restaurant $restaurant, Event $event)
    {
        $dishes = $restaurant->dishes()
            ->with(['dishType', 'diets', 'allergies'])
            ->get();

        return view('menus.user-create', compact('restaurant', 'event', 'dishes'));
    }

    public function storeForUser(Request $request, Restaurant $restaurant, Event $event)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'dishes' => 'required|array|min:1',
        ]);

        $menu = Menu::create([
            'price' => $validated['price'],
            'user_id' => Auth::id(),
            'restaurant_id' => $restaurant->id,
        ]);

        $menu->dishes()->attach($validated['dishes']);

        $event->update([
            'menu_id' => $menu->id,
        ]);

        return redirect()->route('events.show', [
            'restaurant' => $event->restaurant_id,
            'event' => $event->id,
        ])->with('success', 'Menu zostało utworzone i przypisane do wydarzenia.');
    }
}
