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

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'login' => 'Podany email lub hasło są nieprawidłowe.',
            ])->onlyInput('email', 'previous_url');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'login' => 'Twoje konto jest nieaktywne. Skontaktuj się z administratorem.',
            ]);
        }

        if ($user->role_id === 1) {
            return redirect()->intended(route('users.admin-dashboard'));
        }

        if ($user->role_id === 3) {
            return redirect()->intended(route('users.manager-dashboard'));
        }

        return redirect()->intended(route('main.index'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('main.index');
    }
}
