<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\Room;
use App\Models\User;
use App\Models\Event;
use App\Models\Status;
use App\Models\EventType;
use App\Models\Restaurant;
use Illuminate\Support\Str;
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
     * Update status of the resource.
     */
    public function updateStatus(Event $event, Request $request)
    {
        Gate::authorize('restaurant-owner', $event->restaurant);

        $validated = $request->validate([
            'status_id' => 'required|integer|exists:statuses,id'
        ]);

        $event->update(['status_id' => $validated['status_id']]);

        $statusName = Status::find($validated['status_id'])->name;
        $user = Auth::user();

        if ($user->id === $event->manager_id) {
            return redirect()->route('users.manager-dashboard')
                ->with('success', "Status wydarzenia zmieniono na: $statusName.");
        }

        if ($user->role_id === 1) {
            return redirect()->route('users.admin-dashboard')
                ->with('success', "Status wydarzenia zmieniono na: $statusName.");
        }

        return back()->with('success', "Status wydarzenia zmieniono na: $statusName.");
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

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $startTime = $hours[$dateString]['start'] ?? '12:00';
            $endTime = $hours[$dateString]['end'] ?? '20:00';
            $selectedRoomIds = $roomsPerDay[$dateString] ?? [];

            $busyRoomIds = $this->getBusyRoomIds($dateString, $startTime, $endTime, $restaurant->id);
            $conflictingRooms = array_intersect($selectedRoomIds, $busyRoomIds);

            if (!empty($conflictingRooms)) {
                $roomNames = Room::whereIn('id', $conflictingRooms)->pluck('name')->implode(', ');
                return back()->with('error', "W dniu $dateString sala/sale ($roomNames) są zajęte lub w trakcie sprzątania.")->withInput();
            }
        }

        $action = $request->input('action');
        $reservationId = (string) Str::uuid();
        $createdEvents = [];
        $dayCounter = 1;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $startTime = $hours[$dateString]['start'] ?? '12:00';
            $endTime = $hours[$dateString]['end'] ?? '20:00';
            $dailyPeopleCount = $peoplePerDay[$dateString];

            $description = $request->description;
            if ($startDate->ne($endDate)) {
                $description .= " (Dzień {$dayCounter})";
            }
            $selectedRoomIds = $roomsPerDay[$dateString] ?? [];
            $selectedMenuIds = $menusPerDay[$dateString] ?? [];

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
                'original_data' => [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'number_of_people' => $dailyPeopleCount,
                    'description' => $description,
                    'rooms' => $selectedRoomIds,
                    'menus' => $selectedMenuIds
                ],
            ]);

            if (!empty($selectedRoomIds)) {
                $event->rooms()->sync($selectedRoomIds);
            }

            if (!empty($selectedMenuIds)) {
                $menusWithPivot = [];
                foreach ($selectedMenuIds as $menuId) {
                    $menusWithPivot[$menuId] = ['amount' => $dailyPeopleCount];
                }
                $event->menus()->sync($menusWithPivot);
            }

            $createdEvents[] = $event;
            $dayCounter++;
        }

        if ($action === 'custom') {
            return redirect()->route('menus.user-create', [
                'restaurant' => $restaurant->id,
                'event' => $createdEvents[0]->id,
            ])->with('info', 'Wydarzenia utworzone. Możesz teraz skomponować własne menu.');
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

        Gate::authorize('manage-event', $event);

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
        $user = Auth::user();

        $selectedRoomIds = $request->input('rooms', []);
        $busyRoomIds = $this->getBusyRoomIds($request->date, $request->start_time, $request->end_time, $event->restaurant_id, $event->id);
        $conflictingRooms = array_intersect($selectedRoomIds, $busyRoomIds);

        if (!empty($conflictingRooms)) {
            $roomNames = Room::whereIn('id', $conflictingRooms)->pluck('name')->implode(', ');
            return back()->with('error', "Sala/sale ($roomNames) są zajęte lub w trakcie sprzątania w tym terminie.")->withInput();
        }

        $event->update($request->validated());
        $event->rooms()->sync($selectedRoomIds);

        $menus = $request->input('menus_id', []);
        $event->menus()->sync($menus);

        if ($user->id === $event->user_id) {
            $event->update([
                'original_data' => [
                    'start_time' => $request->input('start_time'),
                    'end_time' => $request->input('end_time'),
                    'number_of_people' => $request->input('number_of_people'),
                    'description' => $request->input('description'),
                    'rooms' => $selectedRoomIds,
                    'menus' => $menus
                ]
            ]);
        }

        if ($user->id === $event->manager_id) {
            return redirect()->route('users.manager-dashboard')->with('success', 'Wydarzenie zostało zaktualizowane.');
        }

        if ($user->id === $event->user_id) {
            return redirect()->route('users.user-dashboard')->with('success', 'Wydarzenie zostało zaktualizowane (zapisano jako nową wersję).');
        }

        return redirect()->route('users.admin-dashboard')->with('success', 'Wydarzenie zostało zaktualizowane.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('users.manager-dashboard')->with('success', 'Wydarzenie usunięte.');
    }

    /**
     * Return events for calendar.
     */
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

    private function getBusyRoomIds($date, $startTime, $endTime, $restaurantId, $excludeEventId = null)
    {
        $requestedStart = Carbon::parse($date . ' ' . $startTime);
        $requestedEnd = Carbon::parse($date . ' ' . $endTime);

        $query = Event::where('restaurant_id', $restaurantId)
            ->where('date', $date)
            ->where('status_id', '!=', 3)
            ->with('rooms');

        if ($excludeEventId) {
            $query->where('id', '!=', $excludeEventId);
        }

        $eventsToCheck = $query->get();
        $busyRoomIds = [];

        foreach ($eventsToCheck as $event) {
            $eventStart = Carbon::parse($event->date . ' ' . $event->start_time);
            $eventEnd = Carbon::parse($event->date . ' ' . $event->end_time);

            foreach ($event->rooms as $room) {
                $cleaningMinutes = $room->cleaning_duration ?? 0;
                $effectiveRoomEnd = $eventEnd->copy()->addMinutes($cleaningMinutes);

                if ($requestedStart->lessThan($effectiveRoomEnd) && $requestedEnd->greaterThan($eventStart)) {
                    $busyRoomIds[] = $room->id;
                }
            }
        }
        return array_unique($busyRoomIds);
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

        $busyRoomIds = $this->getBusyRoomIds(
            $request->date,
            $request->start_time,
            $request->end_time,
            $request->restaurant_id,
            $request->exclude_event_id
        );

        return response()->json(array_values($busyRoomIds));
    }

    public function compare(Event $event)
    {
        $original = $event->original_data;

        if (!$original) {
            return back()->with('error', 'Brak danych historycznych dla tego wydarzenia.');
        }

        $originalRoomNames = Room::whereIn('id', $original['rooms'] ?? [])->pluck('name');

        $originalMenus = Menu::with(['dishes.dishType', 'dishes.diets', 'dishes.allergies'])
            ->whereIn('id', $original['menus'] ?? [])
            ->get()
            ->transform(function ($menu) {
                $menu->dishesByType = $menu->dishes->groupBy(fn($dish) => $dish->dishType?->name);
                return $menu;
            });

        $event->load(['menus.dishes.dishType', 'menus.dishes.diets', 'menus.dishes.allergies']);

        $event->menus->transform(function ($menu) {
            $menu->dishesByType = $menu->dishes->groupBy(fn($dish) => $dish->dishType?->name);
            return $menu;
        });

        return view('events.compare', compact('event', 'original', 'originalRoomNames', 'originalMenus'));
    }
}
