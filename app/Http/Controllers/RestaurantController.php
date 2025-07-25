<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


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
        return view('restaurants.create', [
            'address' => Address::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Gate::allows('admin-or-manager')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:6',
            'building_number' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $address = Address::create([
                'city' => $request->city,
                'street' => $request->street,
                'postal_code' => $request->postal_code,
                'building_number' => $request->building_number,
            ]);

            Restaurant::create([
                'name' => $request->name,
                'description' => $request->description,
                'address_id' => $address->id,
                'user_id' => Auth::id(),
            ]);
        });

        return redirect()->route('users.manager-dashboard')->with('success', 'Lokal został dodany.');
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
    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        if (!Gate::allows('restaurant-owner', $restaurant)) {
            abort(403);
        }

        $user = User::where('email', $request->input('user_email'))->first();

        if (!$user) {
            return back()->withErrors(['user_email' => 'Nie znaleziono użytkownika o podanym adresie e-mail.'])->withInput();
        }
        if ($user->role_id !== 3) {
            return back()->withErrors(['user_email' => 'Użytkownik musi mieć przypisaną rolę Menadżera.'])->withInput();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_email' => 'required|email|exists:users,email',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
        ]);

        DB::transaction(function () use ($request, $id, $user) {
            $restaurant = Restaurant::findOrFail($id);
            $restaurant->update([
                'name' => $request->name,
                'description' => $request->description,
                'user_id' => $user->id,
            ]);
            $restaurant->address->update([
                'street' => $request->street,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);
        });
        return redirect()->route('users.manager-dashboard')->with('success', 'Dane lokalu zostały zmienione.');
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
