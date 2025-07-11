<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        $restaurant = Restaurant::with('address')->findOrFail($id);
        $users = User::all();

        return view('restaurants.edit', compact('restaurant', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
        ]);

        DB::transaction(function () use ($request, $id) {
            $restaurant = Restaurant::findOrFail($id);
            $restaurant->update([
                'name' => $request->name,
                'description' => $request->description,
                'user_id' => $request->user_id,
            ]);
            $restaurant->address->update([
                'street' => $request->street,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

            $route = $this->redirectRoute();
            return redirect()->route($route)->with('success', 'Zaktualizowano restaurację.');
        });
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
