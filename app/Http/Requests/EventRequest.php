<?php

namespace App\Http\Requests;

use App\Models\Event;
use Carbon\Carbon;
use App\Models\Room;
use Illuminate\Validation\Validator;
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
            'date' => 'required|date|after_or_equal:today',
            'number_of_people' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
            'menus_id' => 'array|min:1',
            'menus_id.*' => 'exists:menus,id',
            'action' => 'in:event,custom',
            'rooms' => 'required|array|min:1',
            'rooms.*' => 'exists:rooms,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
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
            'rooms.required' => 'Musisz wybrać co najmniej jedną salę.',
            'rooms.array' => 'Nieprawidłowy format danych sal.',
            'rooms.*.exists' => 'Wybrana sala jest nieprawidłowa.',
            'start_time.required' => 'Podaj godzinę rozpoczęcia wydarzenia.',
            'start_time.date_format' => 'Nieprawidłowy format godziny rozpoczęcia.',
            'end_time.required' => 'Podaj godzinę zakończenia wydarzenia.',
            'end_time.date_format' => 'Nieprawidłowy format godziny zakończenia.',
            'end_time.after' => 'Godzina zakończenia musi być późniejsza niż rozpoczęcia.',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($this->rooms) {
                $totalCapacity = Room::whereIn('id', $this->rooms)->sum('capacity');
                $numPeople = (int) $this->number_of_people;

                if ($numPeople > $totalCapacity) {
                    $validator->errors()->add(
                        'number_of_people',
                        "Liczba osób ({$numPeople}) przekracza łączną liczbę miejsc w wybranych salach ({$totalCapacity})."
                    );
                }
            }
            $selectedDate = isset($this->date) ? Carbon::parse($this->date) : null;
            $now = Carbon::now();

            if ($selectedDate && $selectedDate->isToday()) {
                if (isset($this->start_time) && Carbon::parse($this->start_time)->lessThan($now)) {
                    $validator->errors()->add('start_time', 'Nie można ustawić godziny rozpoczęcia w przeszłości.');
                }
                if (isset($this->end_time) && Carbon::parse($this->end_time)->lessThan($now)) {
                    $validator->errors()->add('end_time', 'Nie można ustawić godziny zakończenia w przeszłości.');
                }
            }

            if (isset($this->start_time, $this->end_time)) {
                if (Carbon::parse($this->end_time)->lessThanOrEqualTo(Carbon::parse($this->start_time))) {
                    $validator->errors()->add('end_time', 'Godzina zakończenia musi być późniejsza niż godzina rozpoczęcia.');
                }
            }
        });

        $validator->after(function ($validator) {

            if (!$this->date || !$this->start_time || !$this->end_time || empty($this->rooms)) {
                return;
            }

            $start = Carbon::createFromFormat('H:i', $this->start_time);
            $end   = Carbon::createFromFormat('H:i', $this->end_time);
            $eventId = $this->route('id');

            foreach ($this->rooms as $roomId) {
                $query = Event::whereDate('date', $this->date)
                    ->whereHas('rooms', function ($q) use ($roomId) {
                        $q->where('rooms.id', $roomId);
                    })
                    ->where(function ($q) use ($start, $end) {
                        $q->where('start_time', '<', $end->format('H:i'))
                            ->where('end_time', '>', $start->format('H:i'));
                    })
                    ->where('status_id', '!=', 3);

                if ($eventId) {
                    $query->where('id', '!=', $eventId);
                }

                if ($query->exists()) {
                    $validator->errors()->add(
                        'rooms',
                        'Jedna z wybranych sal jest już zajęta w podanym przedziale godzin.'
                    );
                    break;
                }
            }
        });
    }
}
