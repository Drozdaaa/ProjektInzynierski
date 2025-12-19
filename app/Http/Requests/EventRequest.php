<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'required|string|max:1000',
            'event_type_id' => 'required|exists:event_types,id',

            'people' => 'nullable|array',
            'people.*' => 'integer|min:1',

            'hours' => 'required|array',
            'hours.*.start' => 'required|date_format:H:i',
            'hours.*.end' => 'required|date_format:H:i|after:hours.*.start',

            'rooms' => 'required|array',
            'rooms.*' => 'required|array|min:1',
            'rooms.*.*' => 'exists:rooms,id',

            'menus' => 'nullable|array',
            'menus.*' => 'array',
            'menus.*.*' => 'exists:menus,id',
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => 'Pole "Data początkowa" jest wymagane.',
            'start_date.after_or_equal' => 'Data początkowa nie może być wcześniejsza niż dzisiaj.',

            'end_date.required' => 'Pole "Data końcowa" jest wymagane.',
            'end_date.after_or_equal' => 'Data końcowa nie może być wcześniejsza niż data początkowa.',

            'people.required' => 'Musisz podać liczbę osób dla wybranych dni.',
            'people.*.required' => 'Liczba osób jest wymagana dla każdego dnia.',
            'people.*.min' => 'Liczba osób musi wynosić minimum 1.',

            'description.required' => 'Opis wydarzenia jest wymagany.',
            'event_type_id.required' => 'Wybór typu wydarzenia jest wymagany.',
            'terms.accepted' => 'Musisz zaakceptować regulamin rezerwacji.',

            'hours.required' => 'Musisz wybrać daty w kalendarzu, aby wygenerować formularz godzin.',

            'rooms.required' => 'Nie wybrano żadnych sal.',
            'rooms.*.required' => 'Musisz wybrać przynajmniej jedną salę dla każdego dnia.',

            'hours.*.start.required' => 'Godzina rozpoczęcia jest wymagana dla każdego dnia.',
            'hours.*.end.after' => 'Godzina zakończenia musi być późniejsza niż rozpoczęcia.',
        ];
    }
}
