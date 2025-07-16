<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with(['user', 'eventType', 'status'])->get();

        return view('users.manager-dashboard', [
            'events'=>$events,
        ]);
    }

    public function archive(Event $event){
        $event->update(['status_id'=>2]);
        return redirect()->route('users.manager-dashboard')->with('success', 'Wydarzenie zarchiwizowane.');
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
    public function show($id)
    {
        $event = Event::with('menu.dishes.dishType')->findOrFail($id);

        return view('menu.show', [
            'event' => $event,
            'menu' => $event->menu,
            'dishes' => $event->menu->dishes,
        ]);
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
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('users.manager-dashboard')->with('success', 'Wydarzenie usunięte.');
    }
}
