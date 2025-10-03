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
                            <div class="card-body">
                                <h5 class="card-title">{{ $menu->name }}</h5>

                                @if ($menu->dishes->isEmpty())
                                    <p class="text-muted"><em>Brak przypisanych dań.</em></p>
                                @else
                                    <ul class="list-group list-group-flush mb-3">
                                        @foreach ($menu->dishesByType as $type => $dishes)
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
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#menuDetailsModal{{ $menu->id }}">
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
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="menu_id"
                                            id="menu_{{ $menu->id }}" value="{{ $menu->id }}"
                                            @checked(old('menu_id') == $menu->id)>
                                        <label class="form-check-label" for="menu_{{ $menu->id }}">
                                            Wybierz
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('shared.modal', ['menu' => $menu])
                @endforeach
            </div>
        @endif
    </div>
</body>
