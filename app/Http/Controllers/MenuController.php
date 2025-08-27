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
        $user = Auth::user();

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
     * Show the form for creating a new resource.
     */
    public function create(Restaurant $restaurant)
    {
        if (Gate::denies('restaurant-owner', $restaurant)) {
            abort(403);
        }

        $dishes = $restaurant->dishes()->with(['dishType', 'diets', 'allergies'])->get();

        return view('menus.create', compact('restaurant', 'dishes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        if (Gate::denies('restaurant-owner', $restaurant)) {
            abort(403);
        }

        $request->validate([
            'price' => 'required|numeric|min:0',
            'dishes' => 'required|array|min:1',
            'dishes.*' => 'exists:dishes,id',
        ], [
            'dishes.required' => 'Musisz wybrać przynajmniej jedno danie do menu.',
        ]);

        $menu = $restaurant->menus()->create([
            'price' => $request->price,
            'user_id' => Auth::id(),
        ]);

        $menu->dishes()->sync($request->input('dishes'));

        return redirect()->route('menus.create', ['restaurant' => $restaurant->id])
            ->with('success', 'Menu zostało utworzone.');
    }


    /**
     * Display the specified resource.
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
     * Show the form for editing the specified resource.
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
}
