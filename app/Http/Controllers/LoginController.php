<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('main.index');
        }
        return view('auth.login');
    }


    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // --- NOWY FRAGMENT KODU START ---
            // Sprawdzamy, czy formularz przesłał adres powrotny
            if ($request->has('redirect_to')) {
                return redirect($request->input('redirect_to'));
            }
            // --- NOWY FRAGMENT KODU KONIEC ---

            // Standardowa logika ról (wykona się tylko, jeśli nie ma redirect_to)
            if($user->role->name === 'Administrator'){
                return redirect()->route('users.admin-dashboard');
            }
            elseif($user->role->name === 'Manager'){
                return redirect()->route('users.manager-dashboard');
            }

            return redirect()->route('main.index');
        }

        return back()->withErrors([
            'login' => 'Podany email lub hasło są nieprawidłowe.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('main.index');
    }

}
