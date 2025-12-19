<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\Status;
use App\Models\EventType;
use App\Models\Restaurant;
use Illuminate\Support\Str;
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

        if (!$restaurant->user->is_active) {
            return redirect()->route('main.index')
                ->with('error', 'Restauracja tymczasowo nie przyjmuje rezerwacji.');
        }

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

        if (!$restaurant->user->is_active) {
            return redirect()->route('main.index')
                ->with('error', 'Restauracja jest niedostępna.');
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $hours = $request->input('hours', []);
        $roomsPerDay = $request->input('rooms', []);
        $menusPerDay = $request->input('menus', []);
        $peoplePerDay = $request->input('people', []);

        $action = $request->input('action');
        $reservationId = (string) Str::uuid7();
        $createdEvents = [];

        $dayCounter = 1;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->format('Y-m-d');

            $startTime = $hours[$dateString]['start'] ?? '12:00';
            $endTime = $hours[$dateString]['end'] ?? '20:00';
            $dailyPeopleCount = $peoplePerDay[$dateString] ?? $request->number_of_people;

            $description = $request->description;
            if ($startDate->ne($endDate)) {
                $description .= " (Dzień {$dayCounter})";
            }

            $event = Event::create([
                'reservation_id' => $reservationId,
                'date' => $dateString,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'number_of_people' => $dailyPeopleCount,
                'description' => $description,
                'event_type_id' => $request->event_type_id,
                'user_id' => Auth::id(),
                'manager_id' => $restaurant->user_id,
                'restaurant_id' => $restaurant->id,
                'status_id' => 1,
            ]);

            if (!empty($roomsPerDay[$dateString])) {
                $event->rooms()->sync($roomsPerDay[$dateString]);
            }

            if (!empty($menusPerDay[$dateString])) {
                $event->menus()->sync($menusPerDay[$dateString]);
            }
            $createdEvents[] = $event;
            $dayCounter++;
        }

        if ($action === 'custom') {
            return redirect()->route('menus.user-create', [
                'restaurant' => $restaurant->id,
                'event' => $createdEvents[0]->id,
            ])->with('info', 'Wydarzenia utworzone. Możesz teraz edytować menu.');
        }

        return redirect()->route('events.show', [
            'restaurant' => $restaurant->id,
            'event' => $createdEvents[0]->id,
        ])->with('success', 'Rezerwacja została utworzona.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant, Event $event)
    {
        if ($event->restaurant_id !== $restaurant->id) {
            abort(404);
        }
        $events = Event::where('reservation_id', $event->reservation_id)
            ->orderBy('date')
            ->orderBy('start_time')
            ->with([
                'eventType',
                'status',
                'restaurant.address',
                'menus.dishes.dishType',
                'rooms',
                'menus.dishes.diets',
            'menus.dishes.allergies',
            ])
            ->get();

        return view('events.show', compact('events', 'restaurant'));
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
                        'end' => $event->date . 'T' . $event->end_time,
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
            'exclude_event_id' => 'nullable|integer'
        ]);

        $query = Event::where('restaurant_id', $request->restaurant_id)
            ->where('date', $request->date)
            ->where(function ($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->where('status_id', '!=', 3);

        if ($request->has('exclude_event_id') && $request->exclude_event_id) {
            $query->where('id', '!=', $request->exclude_event_id);
        }

        $busyRoomIds = $query->with('rooms:id')
            ->get()
            ->pluck('rooms')
            ->flatten()
            ->pluck('id')
            ->unique();

        return response()->json($busyRoomIds);
    }
}
