<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string| max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id|not_in:1',
        ];
    }
    public function messages(): array
    {
        return [
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

        ];
    }
}
