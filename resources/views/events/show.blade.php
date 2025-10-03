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
                    <strong>Data:</strong> {{ \Carbon\Carbon::parse($event->date)->format('d.m.Y H:i') }} <br>
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

                @if ($event->menu)
                    <h5 class="mt-3">Menu</h5>
                    <p class="mb-2"><strong>Cena:</strong> {{ $event->menu->price }} zł</p>
                    <ul class="list-group">
                        @foreach ($event->menu->dishes as $dish)
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
                @else
                    <p class="text-muted mt-2">Brak przypisanego menu do tego wydarzenia.</p>
                @endif
            </div>

            <div class="card-footer text-end">
                <a href="{{ route('main.index') }}" class="btn btn-secondary">Powrót</a>
            </div>
        </div>
    </div>
</body>
