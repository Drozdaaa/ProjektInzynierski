<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[A-ZĄĆĘŁŃÓŚŻŹ][a-ząćęłńóśżź]+$/u',
            ],
            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[A-ZĄĆĘŁŃÓŚŻŹ][a-ząćęłńóśżź]+(-[A-ZĄĆĘŁŃÓŚŻŹ][a-ząćęłńóśżź]+)*$/u',
            ],
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => [
                'required',
                'string',
                'regex:/^\+?[0-9]+$/',
                'max:9',
                'min:9',
                'unique:users',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],
            'role_id' => 'required|exists:roles,id|not_in:1',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Imię jest wymagane.',
            'first_name.regex' => 'Imię musi zaczynać się wielką literą.',
            'first_name.min' => 'Imię musi mieć co najmniej :min znaki.',

            'last_name.required' => 'Nazwisko jest wymagane.',
            'last_name.regex' => 'Nazwisko musi zaczynać się wielką literą.',

            'email.required' => 'Email jest wymagany.',
            'email.email' => 'Podaj poprawny adres email.',
            'email.unique' => 'Ten email jest już zajęty.',

            'phone.required' => 'Numer telefonu jest wymagany.',
            'phone.unique' => 'Ten numer telefonu jest już zajęty.',

            'password.required' => 'Hasło jest wymagane.',
            'password.min' => 'Hasło musi mieć co najmniej :min znaków.',
            'password.confirmed' => 'Hasła nie są takie same.',
            'password.regex' => 'Hasło musi zawierać co najmniej jedną wielką literę oraz jedną cyfrę.',

            'role_id.required' => 'Wybierz rolę użytkownika.',
            'role_id.exists' => 'Wybrana rola nie istnieje.',
            'role_id.not_in' => 'Nie można przypisać roli administratora.',
        ];
    }
}
