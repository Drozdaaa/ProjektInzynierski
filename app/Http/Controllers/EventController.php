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
use Illuminate\Auth\Events\Validated;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function updateStatus(Event $event, Request $request)
    {
        $validated = $request->validate([
            'status_id' => 'required|integer|in:2,3'
        ]);

        $event->update(['status_id' => $validated['status_id']]);

        $statusName = $validated['status_id'] === 2 ? 'zaplanowane' : 'zarchiwizowane';


        return redirect()->route('users.manager-dashboard')->with('success', "Wydarzenie zostało $statusName.");
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $eventTypes = EventType::all();
        $menus = $restaurant->menus;

        return view('events.create', compact('restaurant', 'eventTypes', 'menus'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        Event::create([
            'date' => $request->date,
            'number_of_people' => $request->number_of_people,
            'description' => $request->description,
            'event_type_id' => $request->event_type_id,
            'menu_id' => $request->menu_id,
            'user_id' => Auth::id(),
            'status_id' => 1,
            'restaurant_id' => $restaurant->id,
            'manager_id' => $restaurant->user_id,
        ]);

        return redirect()->route('main.index')->with('success', 'Wydarzenie zostało utworzone.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $event = Event::with('user')->findOrFail($id);

        return view('events.edit', [
            'event' => $event,
            'users' => User::all(),
            'statuses' => Status::all(),
            'eventTypes' => EventType::all(),
            'menus'=>Menu::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventRequest $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->update($request->validated());

        return redirect()->route('users.manager-dashboard')->with('success', 'Wydarzenie zostało zaktualizowane.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('users.manager-dashboard')->with('success', 'Wydarzenie usunięte.');
    }
}
