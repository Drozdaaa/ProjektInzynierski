<?php

namespace App\Http\Controllers;

use App\Models\DishType;
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
            ->first();

        if (!$restaurant) {
            return view('menus.index', [
                'restaurant' => null,
            ]);
        }

        $restaurant->menus->each(function ($menu) {
            $menu->dishesByType = $menu->dishes->groupBy(
                fn($dish) => optional($dish->dishType)->name ?? 'Inne'
            );
        });

        return view('menus.index', compact('restaurant'));
    }


    /**
     * Show the form for creating a new resource.
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
     * Display a menu assigned to resource.
     */
    public function show($id)
    {
        $event = Event::with('menus.dishes.dishType')->findOrFail($id);
        return view('menus.show', [
            'event' => $event,
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

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
            'dishes.*' => 'exists:dishes,id',
        ], [
            'dishes.required' => 'Musisz wybrać przynajmniej jedno danie.',
        ]);

        $menu = Menu::create([
            'price' => $validated['price'],
            'user_id' => Auth::id(),
            'restaurant_id' => $restaurant->id,
        ]);

        $menu->dishes()->attach($validated['dishes']);
        $event->menus()->attach($menu->id);

        if ($request->has('create_another')) {
            return redirect()
                ->route('menus.user-create', [$restaurant->id, $event->id])
                ->with('success', 'Menu zapisane. Możesz dodać kolejne.');
        }

        return redirect()
            ->route('events.show', [
                'restaurant' => $restaurant->id,
                'event' => $event->id
            ])
            ->with('success', 'Menu zapisane i przypisane do wydarzenia.');
    }

    public function editForUser(Event $event, Request $request)
    {
        $user = Auth::user();

        $isClient = $event->user_id === $user->id;
        $isManagerOrAdmin = Gate::allows('restaurant-owner', $event->restaurant);

        if (!$isClient && !$isManagerOrAdmin) {
            abort(403, 'Brak uprawnień do edycji tego wydarzenia.');
        }

        if ($isClient && $event->status->name !== 'Oczekujące') {
            return redirect()->back()
                ->with('error', 'Jako klient możesz edytować menu tylko dla wydarzeń oczekujących.');
        }

        $cancelUrl = ($user->role_id === 2)
            ? route('users.user-dashboard')
            : route('users.manager-dashboard');

        $restaurant = $event->restaurant;

        $dishes = $restaurant->dishes()
            ->with(['dishType', 'diets', 'allergies'])
            ->get();

        $dishTypes = DishType::orderBy('id')->get();
        $dishTypes->transform(function ($type) use ($dishes) {
            $type->availableDishes = $dishes->where('dish_type_id', $type->id);
            return $type;
        });

        $menus = $event->menus()->with('dishes')->get();

        $menuId = $request->query('menu');
        $menusToEdit = $menuId ? $menus->where('id', $menuId) : $menus;

        return view('menus.user-edit', compact(
            'event',
            'menusToEdit',
            'dishTypes',
            'cancelUrl'
        ));
    }

    public function updateForUser(Request $request, Event $event, Menu $menu)
    {
        $user = Auth::user();

        $isClient = $event->user_id === $user->id;
        $isManagerOrAdmin = Gate::allows('restaurant-owner', $event->restaurant);

        if (!$isClient && !$isManagerOrAdmin) {
            abort(403);
        }

        if ($isClient && $event->status->name !== 'Oczekujące') {
            return redirect()->back()
                ->with('error', 'Nie można edytować menu, ponieważ status wydarzenia uległ zmianie.');
        }

        if (!$event->menus->contains($menu->id)) {
            abort(403, 'To menu nie jest przypisane do tego wydarzenia.');
        }

        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'dishes' => 'required|array|min:1',
            'dishes.*' => 'exists:dishes,id',
        ]);

        $menu->update([
            'price' => $validated['price']
        ]);
        $menu->dishes()->sync($validated['dishes']);

        $msg = $isManagerOrAdmin
            ? 'Menu zostało zaktualizowane przez Managera.'
            : 'Twoje menu zostało zaktualizowane.';

        return redirect()->route('events.show', [
            'restaurant' => $event->restaurant_id,
            'event' => $event->id
        ])->with('success', $msg);
    }

    public function updateAmounts(Request $request, $restaurantId, Event $event)
    {
        $validated = $request->validate([
            'amounts' => 'required|array',
            'amounts.*' => 'required|integer|min:0|max:' . $event->number_of_people,
        ], [
            'amounts.*.max' => 'Liczba porcji nie może być większa niż liczba uczestników wydarzenia.',
        ]);

        $totalAssigned = array_sum($validated['amounts']);

        if ($totalAssigned !== $event->number_of_people) {
            return redirect()
                ->back()
                ->withErrors(['amounts' => 'Suma wszystkich porcji musi być równa liczbie uczestników wydarzenia (' . $event->number_of_people . ').'])
                ->withInput();
        }

        foreach ($validated['amounts'] as $menuId => $amount) {
            $event->menus()->updateExistingPivot($menuId, ['amount' => $amount]);
        }

        $user = Auth::user();

        if ($user->role_id === 2) {
            return redirect()->route('users.user-dashboard')
                ->with('success', 'Liczba porcji została zaktualizowana.');
        }

        return redirect()->route('users.manager-dashboard')
            ->with('success', 'Liczba porcji została zaktualizowana przez managera.');
    }
}
