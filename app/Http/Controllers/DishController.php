<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\DishType;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DishController extends Controller
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
    public function create($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $dishTypes = DishType::all();
        if (Gate::denies('restaurant-owner', $restaurant)) {
            abort(403, 'Brak dostępu do tej restauracji.');
        }

        return view('dishes.create', compact('restaurant','dishTypes'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {


        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'dish_type_id' => 'required|exists:event_types,id',

        ], [
            'name.required' => 'Nazwa dania jest wymagana.',
            'price.required' => 'Cena dania jest wymagana.',
            'price.numeric' => 'Cena musi być liczbą.',
            'price.min' => 'Cena nie może być ujemna.',
            'description.max' => 'Opis może mieć maksymalnie 255 znaków'
        ]);

        $user = Auth::user();
        $restaurant = Restaurant::where('user_id', $user->id)->firstOrFail();

        Dish::create([
            'name'=>$request->name,
            'price'=>$request->price,
            'description'=>$request->description,
            'dish_type_id' => $request->dish_type_id,
            'restaurant_id' => $restaurant->id,
        ]);
        return redirect()->route('dishes.create', ['id' => $restaurant->id])
                 ->with('success', 'Danie zostało utworzone.');
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
