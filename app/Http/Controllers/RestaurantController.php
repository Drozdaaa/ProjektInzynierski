<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\RestaurantRequest;

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
    public function store(RestaurantRequest $request)
    {
        $validated = $request->validated();

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('restaurants', 'public');
        }

        $address = Address::create([
            'city' => $validated['city'],
            'street' => $validated['street'],
            'postal_code' => $validated['postal_code'],
            'building_number' => $validated['building_number'],
        ]);

        $restaurant = Restaurant::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'booking_regulations' => $validated['booking_regulations'] ?? null,
            'address_id' => $address->id,
            'user_id' => Auth::id(),
            'image' => $imagePath,
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
    public function update(RestaurantRequest $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        if (!Gate::allows('restaurant-owner', $restaurant) && !Gate::allows('is-admin')) {
            abort(403, 'Brak uprawnień do edycji tego lokalu.');
        }

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($restaurant->image) {
                Storage::disk('public')->delete($restaurant->image);
            }
            $path = $request->file('image')->store('restaurants', 'public');
            $restaurant->image = $path;
        }

        $restaurant->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'booking_regulations' => $validated['booking_regulations'] ?? null,
        ]);

        if (Gate::allows('is-admin') && $request->filled('user_id')) {
            $adminData = $request->validate([
                'user_id' => 'required|integer|exists:users,id'
            ]);

            if ($restaurant->user_id != $adminData['user_id']) {
                $restaurant->menus()->update(['user_id' => $adminData['user_id']]);
                $restaurant->events()->update(['manager_id' => $adminData['user_id']]);
            }

            $restaurant->user_id = $adminData['user_id'];
            $restaurant->save();
        }

        $restaurant->address->update([
            'city' => $validated['city'],
            'street' => $validated['street'],
            'postal_code' => $validated['postal_code'],
            'building_number' => $validated['building_number'],
        ]);

        if (Gate::allows('is-admin')) {
            return redirect()->route('users.admin-dashboard')
                ->with('success', 'Dane restauracji zostały zaktualizowane przez Administratora.');
        }

        return redirect()->route('restaurants.index')
            ->with('success', 'Dane restauracji zostały zaktualizowane.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        if (!Gate::allows('restaurant-owner', $restaurant) && !Gate::allows('is-admin')) {
            abort(403);
        }

        if ($restaurant->image) {
            Storage::disk('public')->delete($restaurant->image);
        }

        $address = $restaurant->address;

        $restaurant->rooms()->delete();
        $restaurant->menus()->delete();
        $restaurant->events()->delete();

        $restaurant->delete();

        if ($address) {
            $address->delete();
        }

        if (Gate::allows('is-admin')) {
            return redirect()->route('users.admin-dashboard')
                ->with('success', 'Restauracja została usunięta przez Administratora.');
        }

        return redirect()->route('users.manager-dashboard')
            ->with('success', 'Restauracja została usunięta.');
    }
}
