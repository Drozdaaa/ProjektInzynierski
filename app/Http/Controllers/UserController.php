<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();

        $events = Event::with([
            'menus',
            'menus.dishes.dishType',
            'menus.dishes.diets',
            'menus.dishes.allergies',
            'status',
            'restaurant.address',
            'rooms',
            'eventType',
        ])
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->paginate(6);

        $events->each(function ($event) {
            $event->menus->each(function ($menu) {
                $menu->dishesByType = $menu->dishes
                    ->groupBy(fn($dish) => $dish->dishType->name);
            });
            $averageMenuPrice = $event->menus->avg('price');
            $event->total_cost = $event->number_of_people * $averageMenuPrice;
        });


        return view('users.user-dashboard', compact('events'));
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
