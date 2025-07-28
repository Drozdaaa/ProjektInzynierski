<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
            'date' => 'required|date|after_or_equal:today',
            'number_of_people' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
            'menu_id' => 'required|exists:menus,id',
        ];
    }
     public function messages(): array
    {
        return [
            'date.required' => 'Pole „Data” jest wymagane.',
            'date.after_or_equal' => 'Data wydarzenia nie może być z przeszłości.',
            'number_of_people.required' => 'Pole „Liczba osób” jest wymagane.',
            'number_of_people.integer' => 'Liczba osób musi być liczbą całkowitą.',
            'number_of_people.min' => 'Liczba osób musi wynosić co najmniej 1.',
            'description.required' => 'Pole „Opis” jest wymagane.',
            'description.max' => 'Opis nie może być dłuższy niż 255 znaków.',
            'event_type_id.required' => 'Musisz wybrać typ wydarzenia.',
            'event_type_id.exists' => 'Wybrany typ wydarzenia jest nieprawidłowy.',
            'menu_id.required' => 'Musisz wybrać menu.',
            'menu_id.exists' => 'Wybrane menu jest nieprawidłowe.',
        ];
    }
}
