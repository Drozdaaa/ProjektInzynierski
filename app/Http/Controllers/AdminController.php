<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Event;
use App\Models\Status;
use App\Models\Address;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $usersQuery = User::with('role')->where('role_id', '!=', 1);

        if ($request->filled('user_search')) {
            $search = $request->user_search;
            $usersQuery->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $usersQuery->where('role_id', $request->role_id);
        }

        if ($request->filled('user_status')) {
            $usersQuery->where('is_active', $request->user_status);
        }

        $users = $usersQuery->orderBy('id')
            ->paginate(10, ['*'], 'users_page')
            ->withQueryString();

        $restaurantsQuery = Restaurant::with(['address', 'user']);

        if ($request->filled('restaurant_search')) {
            $restaurantsQuery->where('name', 'like', "%{$request->restaurant_search}%");
        }

        if ($request->filled('restaurant_city')) {
            $restaurantsQuery->whereHas('address', function ($q) use ($request) {
                $q->where('city', $request->restaurant_city);
            });
        }

        $restaurants = $restaurantsQuery->orderBy('id')
            ->paginate(10, ['*'], 'restaurants_page')
            ->withQueryString();

        $eventsQuery = Event::with(['user', 'restaurant', 'status', 'eventType']);

        if ($request->filled('event_restaurant_id')) {
            $eventsQuery->where('restaurant_id', $request->event_restaurant_id);
        }

        if ($request->filled('status_id')) {
            $eventsQuery->where('status_id', $request->status_id);
        }

        if ($request->filled('date_from')) {
            $eventsQuery->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $eventsQuery->whereDate('date', '<=', $request->date_to);
        }

        $events = $eventsQuery->orderBy('date', 'desc')
            ->orderBy('start_time', 'asc')
            ->paginate(10, ['*'], 'events_page')
            ->withQueryString();

        $cities = Address::select('city')->distinct()->orderBy('city')->pluck('city');
        $roles = Role::where('id', '!=', 1)->get();

        return view('users.admin-dashboard', [
            'users' => $users,
            'restaurants' => $restaurants,
            'events' => $events,
            'managers' => User::where('role_id', 3)->get(),
            'statuses' => Status::all(),
            'filter_restaurants' => Restaurant::orderBy('name')->get(),
            'cities' => $cities,
            'roles' => $roles
        ]);
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('is-admin');

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|string|max:20',
            'role_id' => 'required|integer|exists:roles,id',
            'is_active' => 'nullable'
        ]);

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role_id' => $validated['role_id'],
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('users.admin-dashboard')
            ->with('success', "Dane użytkownika {$user->first_name} {$user->last_name} zostały zaktualizowane.");
    }

    public function destroy(string $id)
    {
        Gate::authorize('is-admin');

        if (in_array((int)$id, [1, 4])) {
            return back()->with('error', 'Nie można usunąć konta Głównego Administratora (Systemowe).');
        }

        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Nie możesz usunąć swojego konta z poziomu panelu administracyjnego.');
        }

        try {
            DB::transaction(function () use ($user) {

                if ($user->role_id == 2) {
                    if ($user->events()->whereIn('status_id', [1, 2])->exists()) {
                        throw new \Exception('Nie można usunąć klienta, który posiada aktywne rezerwacje.');
                    }

                    $user->events()->delete();
                    $user->delete();
                } elseif ($user->role_id == 3) {
                    $restaurants = $user->restaurants()->with('address', 'events', 'rooms', 'menus')->get();

                    $hasActiveEvents = $restaurants->pluck('events')->flatten()
                        ->whereIn('status_id', [1, 2])
                        ->isNotEmpty();

                    if ($hasActiveEvents) {
                        $user->update(['is_active' => false]);
                    } else {
                        foreach ($restaurants as $restaurant) {
                            $restaurant->events()->delete();
                            $restaurant->rooms()->delete();
                            $restaurant->menus()->update(['user_id' => Auth::id()]);
                            $restaurant->forceDelete();

                            if ($restaurant->address) {
                                $restaurant->address->delete();
                            }
                        }

                        $user->delete();
                    }
                } else {
                    $user->events()->delete();
                    $user->delete();
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $userCheck = User::find($id);
        $message = ($userCheck && !$userCheck->is_active)
            ? 'Konto managera zostało dezaktywowane, a nie usunięte (lokal posiada aktywne rezerwacje).'
            : 'Użytkownik został trwale usunięty.';

        return redirect()->route('users.admin-dashboard')->with('success', $message);
    }
}
