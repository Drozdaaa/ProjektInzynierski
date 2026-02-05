<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1|max:10000',
            'price' => 'required|numeric|min:0|max:50000',
            'description' => 'nullable|string|max:255',
            'cleaning_hours' => 'nullable|integer|min:0|max:24',
            'cleaning_minutes' => 'nullable|integer|min:0|max:59',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nazwa sali jest wymagana.',
            'name.string' => 'Nazwa sali musi być tekstem.',
            'name.max' => 'Nazwa sali nie może mieć więcej niż 50 znaków.',

            'capacity.required' => 'Pojemność sali jest wymagana.',
            'capacity.integer' => 'Pojemność sali musi być liczbą całkowitą.',
            'capacity.min' => 'Pojemność sali musi wynosić co najmniej 1 osobę.',
            'capacity.max' => 'Pojemność sali nie może być większa od 10000.',

            'price.required' => 'Cena sali jest wymagana.',
            'price.numeric' => 'Cena sali musi być liczbą.',
            'price.min' => 'Cena sali nie może być ujemna.',
            'price.max' => 'Cena sali nie może przekraczać 50000 zł.',

            'description.string' => 'Opis sali musi być tekstem.',
            'description.max' => 'Opis sali nie może przekraczać 255 znaków.',

            'cleaning_hours.integer' => 'Godziny sprzątania muszą być liczbą całkowitą.',
            'cleaning_hours.min' => 'Godziny sprzątania nie mogą być ujemne.',
            'cleaning_hours.max' => 'Godziny sprzątania mogą trwać maksymalnie 24 godziny.',

            'cleaning_minutes.integer' => 'Minuty sprzątania muszą być liczbą całkowitą.',
            'cleaning_minutes.min' => 'Minuty sprzątania nie mogą być ujemne.',
            'cleaning_minutes.max' => 'Minuty sprzątania muszą mieścić się w zakresie 0-59.',
        ];
    }
}
