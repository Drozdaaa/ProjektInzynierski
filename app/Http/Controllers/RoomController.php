<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create(Restaurant $restaurant)
    {
        if (! Gate::allows('restaurant-owner', $restaurant)) {
            abort(403);
        }

        $restaurant->load('rooms');

        return view('rooms.create', compact('restaurant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        if (! Gate::allows('restaurant-owner', $restaurant)) {
            abort(403);
        }

        if ($request->input('action') === 'finish') {
            return redirect()->route('users.manager-dashboard')
                ->with('success', 'Dodawanie sal zakończone.');
        }

        $request->validate([
            'room_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $restaurant->rooms()->create([
            'name' => $request->room_name,
            'capacity' => $request->capacity,
            'description' => $request->description,
        ]);

        return redirect()->route('rooms.create', $restaurant->id)
            ->with('success', 'Sala została dodana.');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Restaurant $restaurant, Room $room)
    {
        if (! Gate::allows('restaurant-owner', $restaurant)) {
            abort(403);
        }

        if ($room->restaurant_id !== $restaurant->id) {
            abort(404);
        }

        $request->validate([
            'room_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $room->update([
            'name' => $request->room_name,
            'capacity' => $request->capacity,
            'description' => $request->description,
        ]);

        return redirect()->route('restaurants.index')
            ->with('success', 'Sala została zaktualizowana.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Room $room)
    {
        if (! Gate::allows('restaurant-owner', $restaurant)) {
            abort(403);
        }

        if ($room->restaurant_id !== $restaurant->id) {
            abort(404);
        }

        $room->delete();

        return redirect()->route('restaurants.index')
            ->with('success', 'Sala została usunięta.');
    }
}
