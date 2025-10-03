<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use App\Models\Event;
use App\Models\Status;
use App\Models\EventType;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Update status of the event.
     */
    public function updateStatus(Event $event, Request $request)
    {
        $validated = $request->validate([
            'status_id' => 'required|integer|in:2,3'
        ]);

        $event->update(['status_id' => $validated['status_id']]);

        $statusName = $validated['status_id'] === 2 ? 'zaplanowane' : 'zarchiwizowane';

        return redirect()->route('users.manager-dashboard')
            ->with('success', "Wydarzenie zostało $statusName.");
    }

    /**
     * Show the form for creating a new event.
     */
    public function create($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $eventTypes = EventType::all();

        $menus = $restaurant->menus;
        $restaurant->menus->transform(function ($menu) {
            $menu->dishesByType = $menu->dishes->groupBy(fn($dish) => $dish->dishType->name);
            return $menu;
        });

        return view('events.create', compact('restaurant', 'eventTypes', 'menus'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(EventRequest $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $action = $request->input('action');

        $event = Event::create([
            'date' => $request->date,
            'number_of_people' => $request->number_of_people,
            'description' => $request->description,
            'event_type_id' => $request->event_type_id,
            'menu_id' => null,
            'user_id' => Auth::id(),
            'status_id' => 1,
            'restaurant_id' => $restaurant->id,
            'manager_id' => $restaurant->user_id,
        ]);

        if ($action === 'custom') {
            return redirect()->route('menus.user-create', [
                'restaurant' => $restaurant->id,
                'event' => $event->id
            ])->with('info', 'Stwórz własne menu dla tego wydarzenia.');
        }

        $event->update(['menu_id' => $request->menu_id]);

        return redirect()->route('events.show', [
            'restaurant' => $event->restaurant_id,
            'event' => $event->id,
        ])->with('success', 'Wydarzenie zostało utworzone.');
    }


    /**
     * Display the specified resource.
     */
     public function show(Restaurant $restaurant, Event $event)
    {
        if ($event->restaurant_id !== $restaurant->id) {
            abort(404);
        }

        $event->load([
            'eventType',
            'status',
            'restaurant.address',
            'menu.dishes.dishType',
        ]);

        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $event = Event::with(
            'user',
            'restaurant.menus.dishes.dishType',
            'restaurant.menus.dishes.diets',
            'restaurant.menus.dishes.allergies'
        )->findOrFail($id);

        $restaurant = $event->restaurant;

        $restaurant->menus->transform(function ($menu) {
            $menu->dishesByType = $menu->dishes->groupBy(fn($dish) => $dish->dishType?->name ?? 'Inne');
            return $menu;
        });

        $menus = $restaurant->menus;
        $eventTypes = EventType::all();
        $users = User::all();
        $statuses = Status::all();

        return view('events.edit', compact('event', 'restaurant', 'menus', 'eventTypes', 'users', 'statuses'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(EventRequest $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->update($request->validated());

        return redirect()->route('users.manager-dashboard')
            ->with('success', 'Wydarzenie zostało zaktualizowane.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('users.manager-dashboard')
            ->with('success', 'Wydarzenie usunięte.');
    }
}
