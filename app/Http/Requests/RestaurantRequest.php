<?php

namespace App\Http\Requests;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestaurantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('admin-or-manager');
    }

    protected function prepareForValidation(): void
    {
        if ($this->isMethod('post') && !$this->has('user_id') && $this->user()) {
            $this->merge([
                'user_id' => $this->user()->id,
            ]);
        }
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $restaurantId = $this->route('id') ?? $this->route('restaurant');
        return [
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'street' => 'required|string|max:60',
            'city' => 'required|string|max:60',
            'postal_code' => 'required|string|max:6',
            'building_number' => [
                'required',
                'string',
                'max:10',
                'regex:/^[0-9]+[A-Za-z]?(\/[0-9]+[A-Za-z]?)?$/',
            ],

            'booking_regulations' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'user_id' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                Rule::unique('restaurants', 'user_id')->ignore($restaurantId),
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Pole "Nazwa restauracji" jest wymagane.',
            'name.max' => 'Pole "Nazwa restauracji" nie może mieć więcej niż 50 znaków.',

            'description.max' => 'Pole "Opis" nie może mieć więcej niż 255 znaków.',

            'street.required' => 'Pole "Ulica" jest wymagane.',
            'street.max' => 'Pole "Ulica" nie może mieć więcej niż 60 znaków.',

            'city.required' => 'Pole "Miasto" jest wymagane.',
            'city.max' => 'Pole "Miasto" nie może mieć więcej niż 60 znaków.',

            'postal_code.required' => 'Pole "Kod pocztowy" jest wymagane.',
            'postal_code.max' => 'Pole "Kod pocztowy" nie może mieć więcej niż 6 znaków.',

            'building_number.required' => 'Pole "Numer budynku" jest wymagane.',
            'building_number.max' => 'Pole "Numer budynku" może mieć maksymalnie 10 znaków.',
            'building_number.regex' =>'Pole "Numer budynku" może mieć format np. 12, 2B, 36/12 lub 12A/3B.',

            'image.image' => 'Plik musi być poprawnym obrazem.',
            'image.mimes' => 'Dozwolone formaty zdjęcia to: jpeg, png, jpg.',
            'image.max' => 'Zdjęcie nie może być większe niż 2 MB. Zalecane wymiary: 1200x800 px (format poziomy).',
            'image.uploaded' => 'Przesyłanie nie powiodło się. Plik przekracza limit 2MB.',

            'booking_regulations.max' => 'Regulamin rezerwacji nie może przekraczać 5000 znaków.',

            'user_id.unique' => 'Ten użytkownik posiada już przypisaną restaurację. Jeden manager może zarządzać tylko jednym lokalem jednocześnie.',
        ];
    }
}
