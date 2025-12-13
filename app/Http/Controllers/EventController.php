<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Status;
use App\Models\EventType;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\Auth;


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
     * Update status of the resource.
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
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $eventTypes = EventType::all();
        $rooms = $restaurant->rooms;

        $menus = $restaurant->menus;
        $restaurant->menus->transform(function ($menu) {
            $menu->dishesByType = $menu->dishes->groupBy(fn($dish) => $dish->dishType->name);
            return $menu;
        });

        return view('events.create', compact('restaurant', 'eventTypes', 'menus', 'rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $restaurant = Restaurant::findOrFail($id);
        $action = $request->input('action');

        $event = Event::create([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'number_of_people' => $request->number_of_people,
            'description' => $request->description,
            'event_type_id' => $request->event_type_id,
            'user_id' => Auth::id(),
            'status_id' => 1,
            'restaurant_id' => $restaurant->id,
            'manager_id' => $restaurant->user_id,
        ]);
        $event->rooms()->sync($request->rooms);

        if ($action === 'custom') {
            return redirect()->route('menus.user-create', [
                'restaurant' => $restaurant->id,
                'event' => $event->id
            ])->with('info', 'Stwórz własne menu dla tego wydarzenia.');
        }

        $event->menus()->sync($request->menus_id);

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
            'menus.dishes.dishType',
        ]);

        return view('events.show', compact('event', 'restaurant'));
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
            $menu->dishesByType = $menu->dishes->groupBy(fn($dish) => $dish->dishType?->name);
            return $menu;
        });

        $menus = $restaurant->menus;
        $eventTypes = EventType::all();
        $users = User::all();
        $statuses = Status::all();
        $rooms = $restaurant->rooms;

        return view('events.edit', compact('event', 'restaurant', 'menus', 'eventTypes', 'users', 'statuses', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventRequest $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->update($request->validated());
        $rooms = $request->input('rooms', []);
        $event->rooms()->sync($rooms);
        $menus = $request->input('menus_id', []);
        $event->menus()->sync($menus);

        return redirect()
            ->route('users.manager-dashboard')
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

    public function calendar(Restaurant $restaurant)
    {
        $events = Event::where('restaurant_id', $restaurant->id)
            ->with('rooms:id,name')
            ->get()
            ->flatMap(function ($event) {

                return $event->rooms->map(function ($room) use ($event) {

                    $time = substr($event->start_time, 0, 5) . ' - ' . substr($event->end_time, 0, 5);

                    return [
                        'title' => $room->name . ' (' . $time . ')',
                        'start' => $event->date . 'T' . $event->start_time,
                        'end'   => $event->date . 'T' . $event->end_time,
                        'extendedProps' => [
                            'room' => $room->name,
                            'time' => $time,
                        ]
                    ];
                });
            });

        return response()->json($events);
    }

    public function busyRooms(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'restaurant_id' => 'required|exists:restaurants,id',
        ]);

        $busyRoomIds = Event::where('restaurant_id', $request->restaurant_id)
            ->where('date', $request->date)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->with('rooms:id')
            ->get()
            ->pluck('rooms')
            ->flatten()
            ->pluck('id')
            ->unique();

        return response()->json($busyRoomIds);
    }
}
