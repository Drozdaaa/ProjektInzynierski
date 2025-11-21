<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\RestaurantRequest;


class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $restaurant = Restaurant::with('address', 'rooms')->where('user_id', Auth::id())->firstOrFail();

        return view('restaurants.index', compact('restaurant'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('restaurants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RestaurantRequest $request)
    {

        $address = Address::create([
            'date' => $request->date,
            'city' => $request->city,
            'street' => $request->street,
            'postal_code' => $request->postal_code,
            'building_number' => $request->building_number,
        ]);

        $restaurant = Restaurant::create([
            'name' => $request->name,
            'description' => $request->description,
            'address_id' => $address->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('rooms.create', $restaurant->id)
            ->with('success', 'Restauracja została utworzona. Dodaj teraz sale.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        return view('restaurants.show', [
            'restaurant' => $restaurant
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $restaurant = Restaurant::with('address')->findOrFail($id);

        if (!Gate::allows('restaurant-owner', $restaurant)) {
            abort(403);
        }

        $users = User::all();

        return view('restaurants.edit', compact('restaurant', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RestaurantRequest $request, $id)
    {
        $restaurant = Restaurant::with('address')->findOrFail($id);

        if (!Gate::allows('restaurant-owner', $restaurant)) {
            abort(403);
        }

        DB::transaction(function () use ($request, $restaurant) {
            $restaurant->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $restaurant->address->update([
                'street' => $request->street,
                'building_number' => $request->building_number,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);
        });

        $restaurant->refresh();
        $restaurant->load('address');

        return redirect()->route('restaurants.index')
            ->with('success', 'Dane restauracji zostały zaktualizowane.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();
        $route = $this->redirectRoute();
        return redirect()->route($route)->with('success', 'Usunięto restaurację.');
    }

    protected function redirectRoute(): string
    {
        return match (Auth::user()->role->name) {
            'Administrator' => 'users.admin-dashboard',
            'Manager' => 'restaurants.index',
            default => 'restaurants.index',
        };
    }
}
