<?php

namespace App\Http\Controllers;

use App\Models\DishType;
use App\Models\Menu;
use App\Models\Dish;
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

        $events = Event::where('reservation_id', $event->reservation_id)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('menus.user-create', compact('restaurant', 'event', 'dishes', 'events'));
    }

    public function storeForUser(Request $request, Restaurant $restaurant)
    {
        $validated = $request->validate([
            'menus' => 'required|array',
            'menus.*.dishes' => 'nullable|array',
            'menus.*.dishes.*' => 'exists:dishes,id',
        ]);

        $createdCount = 0;
        $firstEventId = null;
        $user = Auth::user();
        $isManager = Gate::allows('restaurant-owner', $restaurant);

        foreach ($validated['menus'] as $eventId => $data) {
            if (empty($data['dishes'])) {
                continue;
            }

            $event = Event::find($eventId);

            if (!$event || $event->restaurant_id != $restaurant->id) {
                continue;
            }

            $isClient = $event->user_id === $user->id;

            if (!$isClient && !$isManager) {
                continue;
            }

            if ($isClient && !$isManager && $event->status->name !== 'Oczekujące') {
                continue;
            }

            if (!$firstEventId) {
                $firstEventId = $event->id;
            }

            $dishes = Dish::whereIn('id', $data['dishes'])->get();
            $totalPrice = $dishes->sum('price');

            $menu = Menu::create([
                'name' => 'Własne menu (' . $event->date . ')',
                'price' => $totalPrice,
                'user_id' => $user->id,
                'restaurant_id' => $restaurant->id,
                'is_custom' => true,
            ]);

            $menu->dishes()->attach($data['dishes']);
            $event->menus()->attach($menu->id, ['amount' => 0]);

            if ($isClient && !$isManager) {
                $originalData = $event->original_data;
                $originalData['menus'] = $event->menus->mapWithKeys(function ($menu) {
                    return [$menu->id => $menu->pivot->amount];
                })->toArray();
                $event->update(['original_data' => $originalData]);
            }

            $createdCount++;
        }

        $redirectEventId = $firstEventId ?? array_key_first($validated['menus']);

        $msg = "Pomyślnie utworzono i przypisano menu.";

        if ($request->has('create_another')) {
            return redirect()
                ->route('menus.user-create', [
                    'restaurant' => $restaurant->id,
                    'event' => $redirectEventId
                ])
                ->with('success', $msg . " Możesz stworzyć kolejne.");
        }

        return redirect()
            ->route('events.show', [
                'restaurant' => $restaurant->id,
                'event' => $redirectEventId
            ])
            ->with('success', $msg);
    }

    public function editForUser(Event $event, Request $request)
    {
        $user = Auth::user();
        $isClient = $event->user_id === $user->id;
        $isManager = Gate::allows('restaurant-owner', $event->restaurant);

        if (!$isClient && !$isManager) {
            abort(403, 'Brak uprawnień do edycji tego wydarzenia.');
        }

        if ($isClient && !$isManager && $event->status->name !== 'Oczekujące') {
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

        $menusToEdit = $event->menus()->with('dishes')->get();

        return view('menus.user-edit', compact(
            'event',
            'menusToEdit',
            'dishTypes',
            'cancelUrl'
        ));
    }

    public function updateForUser(Request $request, Event $event)
    {
        $user = Auth::user();
        $isManager = Gate::allows('restaurant-owner', $event->restaurant);

        if ($event->user_id !== $user->id && !$isManager) {
            abort(403);
        }

        $validated = $request->validate([
            'menus' => 'required|array',
            'menus.*.price' => 'required|numeric|min:0',
            'menus.*.dishes' => 'array',
            'menus.*.dishes.*' => 'exists:dishes,id',
        ]);

        foreach ($validated['menus'] as $menuId => $data) {
            $menu = $event->menus()->find($menuId);

            if (!$menu) continue;

            if ($menu->events()->count() > 1 || $menu->event_id === null) {

                $menu = $this->cloneMenuForEvent($menu, $event);
            }

            $menu->update(['price' => $data['price']]);
            $menu->dishes()->sync($data['dishes'] ?? []);
        }

        $event->load('menus');

        if (!$isManager) {
            $originalData = $event->original_data;
            $originalData['menus'] = $event->menus->mapWithKeys(function ($m) {
                return [$m->id => $m->pivot->amount];
            })->toArray();

            $event->update(['original_data' => $originalData]);
        }

        $msg = $isManager ? 'Menu zostało zaktualizowane przez Managera.' : 'Twoje menu zostało zaktualizowane.';

        return redirect()->route('events.show', [
            'restaurant' => $event->restaurant_id,
            'event' => $event->id
        ])->with('success', $msg);
    }

    public function updateAmounts(Request $request, $restaurantId)
    {
        $request->validate([
            'amounts' => 'required|array',
            'amounts.*' => 'array',
            'amounts.*.*' => 'required|integer|min:0',
        ]);

        $restaurant = Restaurant::findOrFail($restaurantId);
        $user = Auth::user();

        foreach ($request->amounts as $eventId => $menusData) {
            $event = Event::find($eventId);

            if (!$event || $event->restaurant_id != $restaurantId) {
                continue;
            }

            $dailyTotal = array_sum($menusData);

            if ($dailyTotal !== $event->number_of_people) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        "sum_error_{$eventId}" => "Źle podano ilość porcji: wpisano $dailyTotal, a gości jest {$event->number_of_people}."
                    ]);
            }

            foreach ($menusData as $menuId => $amount) {
                $event->menus()->updateExistingPivot($menuId, ['amount' => $amount]);
            }

            if ($user->id === $event->user_id) {
                $originalData = $event->original_data;
                $originalData['menus'] = $menusData;
                $event->update(['original_data' => $originalData]);
            }
        }

        if (Gate::allows('restaurant-owner', $restaurant)) {
            return redirect()->route('users.manager-dashboard')
                ->with('success', 'Liczba porcji została zaktualizowana przez managera.');
        }

        return redirect()->route('users.user-dashboard')
            ->with('success', 'Liczba porcji została zaktualizowana.');
    }

    private function cloneMenuForEvent(Menu $menu, Event $event): Menu
    {
        $pivotInfo = $event->menus->find($menu->id);
        $currentAmount = $pivotInfo ? $pivotInfo->pivot->amount : 0;

        $newMenu = Menu::create([
            'price' => $menu->price,
            'user_id' => Auth::id(),
            'restaurant_id' => $menu->restaurant_id,
            'event_id' => $event->id,
        ]);

        $newMenu->dishes()->sync($menu->dishes->pluck('id')->toArray());

        $event->menus()->detach($menu->id);
        $event->menus()->attach($newMenu->id, ['amount' => $currentAmount]);

        return $newMenu;
    }

    public function detachMenu(Event $event, Menu $menu)
    {
        $isEventOwner = $event->user_id === Auth::id();
        $isRestaurantOwner = Gate::allows('restaurant-owner', $event->restaurant);

        if (! $isEventOwner && ! $isRestaurantOwner) {
            abort(403, 'Nie masz uprawnień do edycji tego wydarzenia.');
        }
        $event->menus()->detach($menu->id);

        return back()->with('success', 'Menu zostało usunięte z wydarzenia.');
    }
}
