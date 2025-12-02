<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $managerId = Auth::id();
        $status = $request->get('status', 'all');

        $events = Event::with([
            'user',
            'eventType',
            'status',
            'rooms',
            'menus.dishes.dishType',
            'menus.dishes.diets',
            'menus.dishes.allergies'
        ])
            ->where('restaurant_id', function ($query) use ($managerId) {
                $query->select('id')
                    ->from('restaurants')
                    ->where('user_id', $managerId);
            })
            ->filterStatus($status)
            ->orderByDesc('date')
            ->paginate(6)
            ->appends(['status' => $status]);

        $events->appends(['status' => $status]);
        $events->each(function ($event) {
            $event->menus->each(function ($menu) {
                $menu->dishesByType = $menu->dishes->groupBy(fn($dish) => $dish->dishType?->name);
            });
        });

        $restaurant = Restaurant::where('user_id', $managerId)
            ->with('menus.dishes.dishType')
            ->first();

        return view('users.manager-dashboard', compact('events', 'restaurant', 'status'));
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
