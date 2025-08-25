@include('shared.html')
@include('shared.head', ['pageTitle' => 'Menu'])

<body>
    @include('shared.navbar')

    <div class="container mt-5">
        <h1 class="mb-4">Twoje aktualne menu</h1>

        <div class="mb-3">
            <a href="{{ route('dishes.create', ['restaurant' => $restaurant->id]) }}" class="btn btn-primary">
                Dodaj danie
            </a>

            <a href="{{ route('menus.create', ['restaurant' => $restaurant->id]) }}" class="btn btn-success">
                Utwórz nowe menu
            </a>
        </div>

        @if ($restaurant->menus->isEmpty())
            <div class="alert alert-info">
                Nie masz jeszcze żadnego menu. Utwórz nowe menu, aby rozpocząć.
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($restaurant->menus as $menu)
                    @php
                        $dishesByType = $menu->dishes->groupBy(fn($dish) => $dish->dishType?->name ?? 'Inne');
                    @endphp
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">{{ $menu->name }}</h5>

                                @if ($menu->dishes->isEmpty())
                                    <p class="text-muted"><em>Brak przypisanych dań.</em></p>
                                @else
                                    <ul class="list-group list-group-flush mb-3">
                                        @foreach ($dishesByType as $type => $dishes)
                                            <li class="list-group-item">
                                                <strong>{{ $type }}:</strong>
                                                {{ $dishes->pluck('name')->join(', ') }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">
                                    Cena menu: {{ $menu->price }} zł
                                </span>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#menuDetailsModal{{ $menu->id }}">
                                        Szczegóły
                                    </button>

                                    <a href="{{ route('menus.edit', ['menu' => $menu->id]) }}"
                                        class="btn btn-info btn-sm">
                                        Edytuj
                                    </a>

                                    <form action="{{ route('menus.destroy', $menu->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Na pewno chcesz usunąć to menu?')">
                                            Usuń
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="menuDetailsModal{{ $menu->id }}" tabindex="-1"
                        aria-labelledby="menuDetailsLabel{{ $menu->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="menuDetailsLabel{{ $menu->id }}">
                                        Szczegóły menu: {{ $menu->name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Zamknij"></button>
                                </div>
                                <div class="modal-body">
                                    @if ($menu->dishes->isEmpty())
                                        <p class="text-muted"><em>Brak przypisanych dań.</em></p>
                                    @else
                                        @foreach ($dishesByType as $type => $dishes)
                                            <h6 class="mt-3">{{ $type }}</h6>
                                            <ul class="list-group mb-3">
                                                @foreach ($dishes as $dish)
                                                    <li class="list-group-item">
                                                        <div class="d-flex justify-content-between">
                                                            <strong>{{ $dish->name }}</strong>
                                                            <span>{{ $dish->price }} zł</span>
                                                        </div>
                                                        <small>Diety:
                                                            {{ $dish->diets->pluck('name')->join(', ') ?: 'Brak' }}</small><br>
                                                        <small>Alergeny:
                                                            {{ $dish->allergies->pluck('name')->join(', ') ?: 'Brak' }}</small>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
