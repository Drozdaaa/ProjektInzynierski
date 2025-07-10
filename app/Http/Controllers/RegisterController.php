<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect('/')->with('success', 'Rejestracja zakończona sukcesem!');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:15', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role_id' => ['required', 'exists:roles,id'],
        ]
        , [
        'first_name.required' => 'Imię jest wymagane.',
        'last_name.required' => 'Nazwisko jest wymagane.',
        'email.required' => 'Email jest wymagany.',
        'email.email' => 'Podaj poprawny adres email.',
        'email.unique' => 'Ten email jest już zajęty.',
        'phone.required' => 'Numer telefonu jest wymagany.',
        'phone.unique' => 'Ten numer telefonu jest już zajęty.',
        'password.required' => 'Hasło jest wymagane.',
        'password.min' => 'Hasło musi mieć przynajmniej :min znaków.',
        'role_id.required' => 'Wybierz rolę użytkownika.',
        'role_id.exists' => 'Wybrana rola nie istnieje.',
    ]);
    }
}
