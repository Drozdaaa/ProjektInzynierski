<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Status;
use App\Models\EventType;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function archive(Event $event)
    {
        $event->update(['status_id' => 2]);
        return redirect()->route('users.manager-dashboard')->with('success', 'Wydarzenie zarchiwizowane.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validatedData = $request->validate([
            'date' => 'required|date',
            'number_of_people' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
        ]);

        $event->update($validatedData);
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
