@include('shared.html')
@include('shared.head', ['pageTitle' => 'Szczegóły wydarzenia'])

<body>
    @include('shared.navbar')

    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow" style="max-width: 1000px; width: 100%;">
            <div class="card-header bg-primary text-white">
                Wydarzenie {{ $event->eventType->name }}
            </div>
            <div class="card-body">
                <h5 class="card-title">{{ $event->eventType->name ?? 'Brak typu' }}</h5>
                <p class="card-text mb-2">
                    <strong>Opis:</strong> {{ $event->description }} <br>
                    <strong>Data:</strong> {{ $event->date }} <br>
                    <strong>Liczba osób:</strong> {{ $event->number_of_people }} <br>
                    <strong>Status:</strong> {{ $event->status->name ?? 'Nieznany' }}
                </p>

                <h5 class="mt-3">Restauracja</h5>
                <p class="card-text mb-2">
                    <strong>{{ $event->restaurant->name }}</strong><br>
                    {{ $event->restaurant->description }} <br>
                    <small>
                        {{ $event->restaurant->address->street }}
                        {{ $event->restaurant->address->building_number }},
                        {{ $event->restaurant->address->postal_code }}
                        {{ $event->restaurant->address->city }}
                    </small>
                </p>

                @if ($event->menus->isNotEmpty())
                    <h5 class="mt-3">Menu dla wydarzenia</h5>

                    <form action="{{ route('menus.update-amounts', [$event->restaurant->id, $event->id]) }}"
                        method="POST">
                        @csrf
                        @foreach ($event->menus as $menu)
                            <div class="mb-4 border p-3 rounded">

                                <p><strong>Cena menu:</strong> {{ $menu->price }} zł</p>

                                <div class="mb-2">
                                    @if ($event->menus->count() > 1)
                                        <label class="form-label">
                                            Ile osób ma dostać to menu:
                                        </label>
                                        <input type="number" name="amounts[{{ $menu->id }}]" class="form-control"
                                            value="{{ $menu->pivot->amount }}">
                                    @else
                                        <input type="hidden" name="amounts[{{ $menu->id }}]" class="form-control"
                                            value="{{ $event->number_of_people }}" readonly>
                                    @endif
                                </div>

                                <ul class="list-group mt-3">
                                    @foreach ($menu->dishes as $dish)
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>{{ $dish->name }}</strong><br>
                                                {{ $dish->description }}<br>
                                                <small>{{ $dish->dishType->name ?? 'Inne' }}</small>
                                            </div>
                                            <span>{{ $dish->price }} zł</span>
                                        </li>
                                    @endforeach
                                </ul>

                            </div>
                        @endforeach

                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary">
                                Zapisz menu
                            </button>
                        </div>

                    </form>
                @else
                    <p class="text-muted mt-2">Brak przypisanego menu do tego wydarzenia.</p>
                @endif
            </div>
        </div>
    </div>
</body>
