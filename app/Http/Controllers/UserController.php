<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $status = $request->get('status', 'all');

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
            ->filterStatus($status)
            ->orderByDesc('date')
            ->paginate(6)
            ->appends(['status' => $status]);

        $events->each(function ($event) {
            $event->menus->each(function ($menu) {
                $menu->dishesByType = $menu->dishes
                    ->groupBy(fn($dish) => $dish->dishType->name);
            });
            $menusCost = $event->menus->sum(function ($menu) {
                $amount = $menu->pivot->amount;
                return $menu->price * $amount;
            });

            $roomsPrice = $event->rooms->sum('price');
            $event->total_cost = $menusCost + $roomsPrice;
        });


        return view('users.user-dashboard', compact('events', 'status'));
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
        if ((int)$id !== Auth::id()) {
            abort(403, 'Nie masz uprawnień do edycji tego profilu.');
        }

        $user = Auth::user();
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ((int)$id !== Auth::id()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->route('users.edit', $user->id)
            ->with('success', 'Twoje dane zostały zaktualizowane.');
    }

    public function updatePassword(Request $request, string $id)
    {
        if ((int)$id !== Auth::id()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8', 'different:current_password'],
        ], [
            'current_password.current_password' => 'Obecne hasło jest nieprawidłowe.',
            'password.confirmed' => 'Potwierdzenie nowego hasła nie pasuje.',
            'password.min' => 'Nowe hasło musi mieć co najmniej 8 znaków.',
            'password.different' => 'Nowe hasło musi różnić się od obecnego.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.edit', $user->id)
            ->with('success', 'Hasło zostało zmienione.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        try {
            DB::transaction(function () use ($user) {
                if ($user->role_id === 2) {
                    $hasActiveEvents = Event::where('user_id', $user->id)
                        ->whereIn('status_id', [1, 2])
                        ->exists();

                    if ($hasActiveEvents) {
                        throw new Exception(
                            'Nie możesz usunąć konta, ponieważ posiadasz aktywne rezerwacje.'
                        );
                    }

                    Event::where('user_id', $user->id)->delete();
                    $user->delete();
                }
            });
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $userCheck = User::find($user->id);

        if ($userCheck && $userCheck->is_active == 0) {
            $message = 'Twoje konto zostało dezaktywowane (lokal posiada aktywne rezerwacje).';
        } else {
            $message = 'Twoje konto zostało trwale usunięte.';
        }

        return redirect()->route('main.index')->with('success', $message);
    }
}
