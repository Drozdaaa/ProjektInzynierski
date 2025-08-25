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
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-3">{{ $menu->name }}</h5>

                                @if (!empty($menu->description))
                                    <p class="text-muted">{{ $menu->description }}</p>
                                @endif

                                @if ($menu->dishes->isEmpty())
                                    <p class="text-muted mt-2"><em>Brak przypisanych dań.</em></p>
                                @else
                                    <ul class="list-group list-group-flush mb-3">
                                        @foreach ($menu->dishes as $dish)
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>{{ $dish->name }}</span>
                                                <span class="fw-bold">{{ $dish->price }} zł</span>
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
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#menuDetailsModal{{ $menu->id }}">
                                        Szczegóły
                                    </button>

                                    <a href="{{ route('menus.edit', ['menu' => $menu->id]) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        Edytuj
                                    </a>

                                    <form action="{{ route('menus.destroy', $menu->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
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
                                    <h6 class="mt-3">Dania w menu</h6>
                                    @if ($menu->dishes->isEmpty())
                                        <p class="text-muted"><em>Brak przypisanych dań.</em></p>
                                    @else
                                        <div class="list-group">
                                            @foreach ($menu->dishes as $dish)
                                                <div class="list-group-item">
                                                    <div class="d-flex justify-content-between">
                                                        <strong>{{ $dish->name }}</strong>
                                                        <span>{{ $dish->price }} zł</span>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-uppercase text-muted">Diety:</small>
                                                        @php
                                                            $dishDiets = method_exists($dish, 'diets')
                                                                ? $dish->diets
                                                                : collect();
                                                        @endphp
                                                        @if ($dishDiets->isNotEmpty())
                                                            <ul class="mb-1">
                                                                @foreach ($dishDiets as $diet)
                                                                    <li>{{ $diet->name }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <p class="text-muted mb-1"><em>Brak</em></p>
                                                        @endif
                                                    </div>

                                                    <div class="mt-1">
                                                        <small class="text-uppercase text-muted">Alergeny:</small>
                                                        @php
                                                            $dishAllergies = method_exists($dish, 'allergies')
                                                                ? $dish->allergies
                                                                : collect();
                                                        @endphp
                                                        @if ($dishAllergies->isNotEmpty())
                                                            <ul class="mb-0">
                                                                @foreach ($dishAllergies as $allergy)
                                                                    <li>{{ $allergy->name }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <p class="text-muted mb-0"><em>Brak</em></p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
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
