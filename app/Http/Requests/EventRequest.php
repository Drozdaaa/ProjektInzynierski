<?php

namespace App\Http\Requests;

use App\Models\Room;
use App\Models\Event;
use App\Models\Restaurant;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return Auth::check();
        }

        $routeParam = $this->route('id');
        $eventId = $routeParam instanceof Event ? $routeParam->id : $routeParam;
        $event = Event::find($eventId);

        if (!$event) {
            return false;
        }

        return Gate::allows('manage-event', $event);
    }


    public function rules(): array
    {
        $restaurantId = null;

        if ($this->isMethod('post')) {
            $routeParam = $this->route('id');
            $restaurantId = $routeParam instanceof Restaurant ? $routeParam->id : $routeParam;
        } else {
            $routeParam = $this->route('id');
            $eventId = $routeParam instanceof Event ? $routeParam->id : $routeParam;
            $event = Event::find($eventId);
            $restaurantId = $event?->restaurant_id;
        }

        $userId = $this->user()?->id;

        $rules = [
            'description' => 'required|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return array_merge($rules, [
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'number_of_people' => [
                    'required',
                    'integer',
                    'min:1',
                    function ($value, $fail) {
                        $roomIds = $this->input('rooms', []);

                        if (empty($roomIds)) {
                            return;
                        }
                        $totalCapacity = Room::whereIn('id', $roomIds)->sum('capacity');

                        if ($value > $totalCapacity) {
                            $fail("Liczba osób ($value) przekracza całkowitą pojemność wybranych sal ($totalCapacity).");
                        }
                    },
                ],
                'rooms' => 'required|array',
                'rooms.*' => [
                    'integer',
                    Rule::exists('rooms', 'id')->where(function ($query) use ($restaurantId) {
                        return $query->where('restaurant_id', $restaurantId);
                    }),
                ],
                'menus_id' => 'nullable|array',
                'menus_id.*' => [
                    'integer',
                    Rule::exists('menus', 'id')->where(function ($query) {
                        $user = Auth::user();
                        if ($user->role_id !== 1) {
                            return $query->where('user_id', $user->id);
                        }
                        return $query;
                    }),
                ],
            ]);
        }

        return array_merge($rules, [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'hours' => 'required|array',
            'hours.*.start' => 'required|date_format:H:i',
            'hours.*.end' => 'required|date_format:H:i|after:hours.*.start',
            'rooms' => 'required|array',
            'rooms.*' => 'array',
            'rooms.*.*' => [
                'integer',
                Rule::exists('rooms', 'id')->where(function ($query) use ($restaurantId) {
                    return $query->where('restaurant_id', $restaurantId);
                }),
            ],
            'people' => 'required|array',
            'people.*' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $date = str_replace('people.', '', $attribute);
                    $roomIds = $this->input("rooms.$date", []);

                    if (empty($roomIds)) {
                        return;
                    }
                    $totalCapacity = Room::whereIn('id', $roomIds)->sum('capacity');

                    if ($value > $totalCapacity) {
                        $fail("W dniu $date liczba osób ($value) przekracza pojemność wybranych sal ($totalCapacity).");
                    }
                },
            ],
            'menus' => 'nullable|array',
            'terms' => 'accepted',
        ]);
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Pole daty jest wymagane.',
            'start_date.required' => 'Pole "Data początkowa" jest wymagane.',
            'start_date.after_or_equal' => 'Data początkowa nie może być wcześniejsza niż dzisiaj.',
            'end_date.required' => 'Pole "Data końcowa" jest wymagane.',
            'end_date.after_or_equal' => 'Data końcowa nie może być wcześniejsza niż data początkowa.',
            'people.required' => 'Musisz podać liczbę osób dla wybranych dni.',
            'people.*.required' => 'Liczba osób jest wymagana dla każdego dnia.',
            'people.*.min' => 'Liczba osób musi wynosić minimum 1.',
            'number_of_people.required' => 'Liczba osób jest wymagana.',
            'description.required' => 'Opis wydarzenia jest wymagany.',
            'description.max' => 'Opis nie może przekraczać 255 znaków.',
            'event_type_id.required' => 'Wybór typu wydarzenia jest wymagany.',
            'terms.accepted' => 'Musisz zaakceptować regulamin rezerwacji.',
            'hours.required' => 'Musisz wybrać daty w kalendarzu, aby wygenerować formularz godzin.',
            'start_time.required' => 'Godzina rozpoczęcia jest wymagana.',
            'end_time.after' => 'Godzina zakończenia musi być późniejsza niż rozpoczęcia.',
            'rooms.*.required' => 'Musisz wybrać przynajmniej jedną salę dla każdego dnia.',
            'rooms.required' => 'Musisz wybrać przynajmniej jedną salę.',
            'hours.*.start.required' => 'Godzina rozpoczęcia jest wymagana dla każdego dnia.',
            'hours.*.end.after' => 'Godzina zakończenia musi być późniejsza niż rozpoczęcia.',
        ];
    }
}
