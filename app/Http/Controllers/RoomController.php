<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Http\Requests\RoomRequest;
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
    public function store(RoomRequest $request, Restaurant $restaurant)
    {
        if (! Gate::allows('restaurant-owner', $restaurant)) {
            abort(403);
        }


        $hours = $request->input('cleaning_hours', 0);
        $minutes = $request->input('cleaning_minutes', 0);
        $totalDuration = ($hours * 60) + $minutes;

        $restaurant->rooms()->create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'price' => $request->price,
            'description' => $request->description,
            'cleaning_duration' => $totalDuration,
        ]);
        if ($request->input('action') === 'finish') {
            return redirect()->route('users.manager-dashboard')
                ->with('success', 'Dodawanie sal zakończone.');
        }

        return redirect()->back()
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
    public function update(RoomRequest $request, Restaurant $restaurant, Room $room)
    {
        if (! Gate::allows('restaurant-owner', $restaurant)) {
            abort(403);
        }

        if ($room->restaurant_id !== $restaurant->id) {
            abort(404);
        }

        $hours = $request->input('cleaning_hours', 0);
        $minutes = $request->input('cleaning_minutes', 0);
        $totalDuration = ($hours * 60) + $minutes;

        $room->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'price' => $request->price,
            'description' => $request->description,
            'cleaning_duration' => $totalDuration,
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
