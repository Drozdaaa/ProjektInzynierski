<?php

namespace App\Http\Controllers;

use App\Models\Diet;
use App\Models\Dish;
use App\Models\Allergy;
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
    public function index(Restaurant $restaurant)
    {
        $dishes = Dish::where('restaurant_id', $restaurant->id)
            ->with(['dishType', 'diets', 'allergies'])
            ->get();

        $dishTypes = DishType::all();
        $allergies = Allergy::all();
        $diets = Diet::all();

        return view('dishes.index', compact('dishes', 'restaurant', 'dishTypes', 'allergies', 'diets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $dishTypes = DishType::all();
        $allergies = Allergy::all();
        $diets = Diet::all();

        if (Gate::denies('restaurant-owner', $restaurant)) {
            abort(403, 'Brak dostępu do tej restauracji.');
        }

        return view('dishes.create', compact('restaurant', 'dishTypes', 'allergies', 'diets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50|min:2',
            'price' => 'required|numeric|min:0|max:50000',
            'description' => 'required|string|max:255',
            'dish_type_id' => 'required|exists:dish_types,id',
            'diets' => 'sometimes|array',
            'diets.*' => 'exists:diets,id',
            'allergies' => 'sometimes|array',
            'allergies.*' => 'exists:allergies,id',
        ], [
            'name.required' => 'Nazwa dania jest wymagana.',
            'name.max' => 'Nazwa dania może mieć mkasymalnie 50 znaków.',
            'name.min' => 'Nazwa dania może mieć minimalnie 2 znaki.',
            'price.required' => 'Cena dania jest wymagana.',
            'price.numeric' => 'Cena musi być liczbą.',
            'price.min' => 'Cena nie może być ujemna.',
            'price.max' => 'Cena dania nie może przekraczać 50000 zł.',
            'description.max' => 'Opis może mieć maksymalnie 255 znaków',
            'dish_type_id.required' => 'Typ dania jest wymagany.',
        ]);

        $user = Auth::user();
        $restaurant = Restaurant::where('user_id', $user->id)->firstOrFail();

        $dish = Dish::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'dish_type_id' => $request->dish_type_id,
            'restaurant_id' => $restaurant->id,
        ]);

        if ($request->filled('diets')) {
            $dish->diets()->sync($request->input('diets'));
        }

        if ($request->filled('allergies')) {
            $dish->allergies()->sync($request->input('allergies'));
        }
        return redirect()->route('dishes.create', ['restaurant' => $restaurant->id])
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
    public function update(Request $request, Dish $dish)
    {
        $restaurant = $dish->restaurant;
        if (Gate::denies('restaurant-owner', $restaurant)) {
            abort(403, 'Brak dostępu do edycji tego dania.');
        }

        $request->validate([
            'name' => 'required|string|max:30',
            'price' => 'required|numeric|min:0|max:50000',
            'description' => 'required|string|max:255',
            'dish_type_id' => 'required|exists:dish_types,id',
            'diets' => 'sometimes|array',
            'diets.*' => 'exists:diets,id',
            'allergies' => 'sometimes|array',
            'allergies.*' => 'exists:allergies,id',
        ], [
            'name.required' => 'Nazwa dania jest wymagana.',
            'price.numeric' => 'Cena musi być liczbą.',
            'price.min' => 'Cena nie może być ujemna.',
            'price.max' => 'Cena dania nie może przekraczać 50000 zł.',
            'description.max' => 'Opis może mieć maksymalnie 255 znaków',
        ]);

        $dish->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'dish_type_id' => $request->dish_type_id,
        ]);

        $dish->diets()->sync($request->input('diets', []));
        $dish->allergies()->sync($request->input('allergies', []));

        return redirect()->route('dishes.index', ['restaurant' => $restaurant->id])
            ->with('success', 'Danie zostało zaktualizowane.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dish $dish)
    {
        $restaurant = $dish->restaurant;

        if (Gate::denies('restaurant-owner', $restaurant)) {
            abort(403, 'Brak dostępu do usunięcia tego dania.');
        }

        $dish->delete();

        return back()->with('success', 'Danie zostało usunięte.');
    }
}
