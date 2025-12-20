<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class RestaurantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('admin-or-manager');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'street' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'postal_code' => 'required|string|max:6',
            'building_number' => 'required|integer|min:1',
            'booking_regulations' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Pole "Nazwa restauracji" jest wymagane.',
            'name.max' => 'Pole "Nazwa restauracji" nie może mieć więcej niż 50 znaków.',

            'description.max' => 'Pole "Opis" nie może mieć więcej niż 255 znaków.',

            'street.required' => 'Pole "Ulica" jest wymagane.',
            'street.max' => 'Pole "Ulica" nie może mieć więcej niż 50 znaków.',

            'city.required' => 'Pole "Miasto" jest wymagane.',
            'city.max' => 'Pole "Miasto" nie może mieć więcej niż 50 znaków.',

            'postal_code.required' => 'Pole "Kod pocztowy" jest wymagane.',
            'postal_code.max' => 'Pole "Kod pocztowy" nie może mieć więcej niż 6 znaków.',

            'building_number.required' => 'Pole "Numer budynku" jest wymagane.',
            'building_number.integer' => 'Pole "Numer budynku" musi być liczbą całkowitą.',
            'building_number.min' => 'Pole "Numer budynku" musi być większe lub równe 1.',

            'image.image' => 'Plik musi być poprawnym obrazem.',
            'image.mimes' => 'Dozwolone formaty zdjęcia to: jpeg, png, jpg.',
            'image.max' => 'Zdjęcie nie może być większe niż 2 MB. Zalecane wymiary: 1200x800 px (format poziomy).',

            'booking_regulations.max' => 'Regulamin rezerwacji nie może przekraczać 5000 znaków.',
        ];
    }
}
