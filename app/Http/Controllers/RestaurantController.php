<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource (Dashboard Managera).
     */
    public function index()
    {
        $restaurant = Restaurant::where('user_id', Auth::id())
            ->with(['address', 'rooms'])
            ->first();

        if (!$restaurant) {
            return redirect()->route('restaurants.create');
        }

        return view('restaurants.index', compact('restaurant'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Restaurant::where('user_id', Auth::id())->exists()) {
            return redirect()->route('restaurants.index')
                ->with('error', 'Posiadasz już utworzoną restaurację.');
        }

        return view('restaurants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'booking_regulations' => 'nullable|string',
            'street' => 'required|string|max:255',
            'building_number' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
        ]);

        $address = Address::create([
            'city' => $request->city,
            'street' => $request->street,
            'postal_code' => $request->postal_code,
            'building_number' => $request->building_number,
        ]);

        $restaurant = Restaurant::create([
            'name' => $request->name,
            'description' => $request->description,
            'booking_regulations' => $request->booking_regulations,
            'address_id' => $address->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('rooms.create', $restaurant->id)
            ->with('success', 'Restauracja została utworzona. Dodaj teraz sale.');
    }

    /**
     * Display the specified resource (Publiczny widok dla klienta).
     */
    public function show($id)
    {
        $restaurant = Restaurant::with(['address', 'rooms', 'menus.dishes'])->findOrFail($id);

        return view('restaurants.show', compact('restaurant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        if (!Gate::allows('restaurant-owner', $restaurant)) {
             abort(403);
        }

        return view('restaurants.edit', compact('restaurant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        if (!Gate::allows('restaurant-owner', $restaurant)) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'booking_regulations' => 'nullable|string',
            'street' => 'required|string|max:255',
            'building_number' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
        ]);

        $restaurant->update([
            'name' => $request->name,
            'description' => $request->description,
            'booking_regulations' => $request->booking_regulations,
        ]);

        $restaurant->address->update([
            'city' => $request->city,
            'street' => $request->street,
            'postal_code' => $request->postal_code,
            'building_number' => $request->building_number,
        ]);

        return redirect()->route('restaurants.index')
            ->with('success', 'Dane restauracji zostały zaktualizowane.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        if (!Gate::allows('restaurant-owner', $restaurant)) {
            abort(403);
        }
        if($restaurant->address) {
             $restaurant->address->delete();
        }

        $restaurant->delete();

        return redirect()->route('users.manager-dashboard')
            ->with('success', 'Restauracja została usunięta.');
    }
}
