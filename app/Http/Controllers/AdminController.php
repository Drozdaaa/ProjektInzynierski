<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function index()
    {
        return view('users.admin-dashboard', [
            'users' => User::with('role')
                ->where('role_id', '!=', 1)
                ->orderBy('id')
                ->get(),
            'restaurants' => Restaurant::with(['address', 'user'])
                ->orderBy('id')
                ->get(),
            'managers' => User::where('role_id', 3)->get()
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
                }
                elseif ($user->role_id == 3) {
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
            ? 'Konto managera zostało dezaktywowane (lokal posiada aktywne rezerwacje).'
            : 'Użytkownik został trwale usunięty.';

        return redirect()->route('users.admin-dashboard')->with('success', $message);
    }
}
