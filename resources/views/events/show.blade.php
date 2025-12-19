@include('shared.html')
@include('shared.head', ['pageTitle' => 'Szczegóły wydarzenia'])

<body>
    @include('shared.navbar')

    <div class="container mt-5 d-flex justify-content-center mb-5">
        <div class="card shadow" style="max-width: 1000px; width: 100%;">

            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Wydarzenie: {{ $events->first()->eventType->name }}</span>
            </div>

            <div class="card-body">

                <div class="row mb-4 border-bottom pb-3">
                    <div class="col-md-6">
                        <h5 class="text-primary">Restauracja</h5>
                        <strong>{{ $restaurant->name }}</strong><br>
                        <span class="text-muted">{{ $restaurant->description }}</span><br>
                        <small>
                            {{ $restaurant->address->street }} {{ $restaurant->address->building_number }},
                            {{ $restaurant->address->postal_code }} {{ $restaurant->address->city }}
                        </small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h5 class="text-primary">Podsumowanie</h5>
                        <p class="mb-1">Całkowita liczba dni: <strong>{{ $events->count() }}</strong></p>
                        <p class="mb-1">Data startu: <strong>{{ $events->first()->date }}</strong></p>
                    </div>
                </div>

                <form action="{{ route('menus.update-amounts', [$restaurant->id, $events->first()->id]) }}"
                    method="POST">
                    @csrf

                    @foreach ($events as $index => $dayEvent)
                        <div class="card mb-4 border-primary">
                            <div class="card-header bg-light fw-bold text-primary">
                                Dzień {{ $index + 1 }} <span class="text-muted fw-normal mx-2">|</span>
                                {{ $dayEvent->date }}
                            </div>
                            <div class="card-body">

                                <div class="row mb-3">
                                    <div class="col-md-4"><strong>Godziny:</strong>
                                        {{ substr($dayEvent->start_time, 0, 5) }} -
                                        {{ substr($dayEvent->end_time, 0, 5) }}</div>
                                    <div class="col-md-4"><strong>Liczba osób:</strong>
                                        {{ $dayEvent->number_of_people }}</div>
                                    <div class="col-md-4"><strong>Opis:</strong> {{ $dayEvent->description }}</div>
                                </div>

                                <div class="mb-3">
                                    <strong class="text-secondary">Sale:</strong>
                                    @if ($dayEvent->rooms->isNotEmpty())
                                        @foreach ($dayEvent->rooms as $room)
                                            <span class="badge bg-secondary me-1">{{ $room->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">Brak</span>
                                    @endif
                                </div>

                                <hr>

                                <h5 class="text-secondary mt-3 mb-3">Menu</h5>

                                @if ($dayEvent->menus->isNotEmpty())
                                    @foreach ($dayEvent->menus as $menu)
                                        <div class="border rounded p-3 mb-3 bg-white shadow-sm">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-bold fs-5">{{ $menu->name }}</span>
                                                <span class="badge bg-success">{{ $menu->price }} zł / os.</span>
                                            </div>

                                            @if ($dayEvent->menus->count() > 1)
                                                <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
                                                    <label class="fw-bold me-2 mb-0">Liczba porcji dla tego
                                                        menu:</label>
                                                    <input type="number"
                                                        name="amounts[{{ $dayEvent->id }}][{{ $menu->id }}]"
                                                        class="form-control border-primary" style="width: 100px;"
                                                        value="{{ $menu->pivot->amount > 0 ? $menu->pivot->amount : '' }}"
                                                        placeholder="0" min="0" required>
                                                </div>
                                            @else
                                                <input type="hidden"
                                                    name="amounts[{{ $dayEvent->id }}][{{ $menu->id }}]"
                                                    value="{{ $dayEvent->number_of_people }}">
                                            @endif

                                            <ul class="list-group list-group-flush small">
                                                @foreach ($menu->dishes as $dish)
                                                    <li class="list-group-item px-0 py-1 border-0">
                                                        <strong>{{ $dish->name }}</strong> -
                                                        {{ $dish->description }}
                                                        <span
                                                            class="badge bg-secondary rounded-pill ms-1">{{ $dish->dishType->name ?? '' }}</span>

                                                        <div class="mt-1">
                                                            @foreach ($dish->diets as $diet)
                                                                <span class="badge bg-success bg-opacity-75"
                                                                    style="font-size: 0.65rem;">{{ $diet->name }}</span>
                                                            @endforeach
                                                            @foreach ($dish->allergies as $allergen)
                                                                <span class="badge bg-warning text-dark"
                                                                    style="font-size: 0.65rem;">{{ $allergen->name }}</span>
                                                            @endforeach
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-warning py-2">Brak wybranego menu na ten dzień.</div>
                                @endif

                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button type="submit" class="btn btn-primary btn-md">
                            Zapisz zmiany dla wszystkich dni
                        </button>
                        <a href="{{ route('users.user-dashboard') }}" class="btn btn-secondary btn-md">
                            Przejdź do panelu użytkownika
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
