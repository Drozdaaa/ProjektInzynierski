<div class="col-md-3">
    <div class="card dish-card {{ !empty($selected) && $selected ? 'selected' : '' }}"
        data-dish-id="{{ $dish->id }}" data-price="{{ $dish->price }}" style="cursor: pointer;">
        <div class="card-body">
            <h5 class="card-title">{{ $dish->name }}</h5>
            <p class="card-text mb-1">{{ $dish->description }}</p>
            <p class="mb-1"><strong>Typ:</strong> {{ $dish->dishType?->name }}</p>
            <p class="mb-1"><strong>Cena:</strong> {{ $dish->price }} zł</p>

            @if ($dish->diets->isNotEmpty())
                <p class="mb-1"><small>Diety:
                    @foreach ($dish->diets as $d)
                        <span class="badge bg-info text-white">{{ $d->name }}</span>
                    @endforeach
                </small></p>
            @endif

            @if ($dish->allergies->isNotEmpty())
                <p class="mb-1"><small>Alergie:
                    @foreach ($dish->allergies as $a)
                        <span class="badge bg-warning text-white">{{ $a->name }}</span>
                    @endforeach
                </small></p>
            @endif
        </div>

        <div class="card-footer">
            <div class="form-check">
                <input class="form-check-input dish-checkbox" type="checkbox" name="dishes[]"
                    value="{{ $dish->id }}" id="dish_check_{{ $dish->id }}"
                    {{ !empty($selected) && $selected ? 'checked' : '' }} style="pointer-events: none;">
                <label class="form-check-label" for="dish_check_{{ $dish->id }}">
                    Zaznacz
                </label>
            </div>
        </div>
    </div>
</div>
