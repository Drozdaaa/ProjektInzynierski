@include('shared.html')
@include('shared.head', ['pageTitle' => $restaurant->name])

<body>
    @include('shared.navbar')
    <div class="container-fluid mt-5 px-5">
        <h1>{{ $restaurant->name }}</h1>
        <h3>Dodaj nowe wydarzenie</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('events.store', ['id' => $restaurant->id]) }}">
            @csrf

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="date" class="form-label">Data</label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ old('date') }}"
                        required>
                </div>
                <div class="col-md-4">
                    <label for="start_time" class="form-label">Godzina rozpoczęcia</label>
                    <input type="time" name="start_time" id="start_time" class="form-control"
                        value="{{ old('start_time') }}" required>
                </div>

                <div class="col-md-4">
                    <label for="end_time" class="form-label">Godzina zakończenia</label>
                    <input type="time" name="end_time" id="end_time" class="form-control"
                        value="{{ old('end_time') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="number_of_people" class="form-label">Liczba osób</label>
                    <input type="number" name="number_of_people" id="number_of_people" class="form-control"
                        value="{{ old('number_of_people') }}" min="1" required>
                </div>

                <div class="col-md-6">
                    <label for="event_type_id" class="form-label">Typ wydarzenia</label>
                    <select name="event_type_id" id="event_type_id" class="form-select" required>
                        <option value="">-- Wybierz typ --</option>
                        @foreach ($eventTypes as $type)
                            <option value="{{ $type->id }}" @selected(old('event_type_id') == $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>

                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Opis</label>
                <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Wybierz salę (lub sale)</label>
                <div class="d-flex flex-wrap gap-3">
                    @foreach ($rooms as $room)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="rooms[]" id="room{{ $room->id }}"
                                value="{{ $room->id }}"
                                {{ in_array($room->id, old('rooms', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="room{{ $room->id }}">
                                {{ $ro@include('shared.html')
@include('shared.head', ['pageTitle' => 'Menu'])

<body>
    @include('shared.navbar')

    <div class="container mt-4">
        <!-- Nagłówek i przyciski akcji -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h2 mb-3">Twoje aktualne menu</h1>

                <div class="d-flex flex-column flex-md-row gap-2 mb-4">
                    <a href="{{ route('dishes.index', ['restaurant' => $restaurant->id]) }}"
                       class="btn btn-outline-primary">
                        <i class="bi bi-list-ul"></i> Pokaż dania
                    </a>

                    <a href="{{ route('dishes.create', ['restaurant' => $restaurant->id]) }}"
                       class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle"></i> Dodaj danie
                    </a>

                    <a href="{{ route('menus.create', ['restaurant' => $restaurant->id]) }}"
                       class="btn btn-success">
                        <i class="bi bi-journal-plus"></i> Utwórz nowe menu
                    </a>
                </div>
            </div>
        </div>

        @if ($restaurant->menus->isEmpty())
            <!-- Brak menu -->
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info text-center py-4">
                        <i class="bi bi-info-circle display-4 d-block mb-3"></i>
                        <h3>Nie masz jeszcze żadnego menu</h3>
                        <p class="mb-0">Utwórz pierwsze menu, aby rozpocząć.</p>
                    </div>
                </div>
            </div>
        @else
            <!-- Karty menu -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                @foreach ($restaurant->menus as $menu)
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0">
                            <!-- Nagłówek karty -->
                            <div class="card-header bg-light py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="card-title mb-0 text-truncate pe-2">{{ $menu->name }}</h5>
                                    <span class="badge bg-success fs-6">
                                        {{ number_format($menu->price, 2) }} zł
                                    </span>
                                </div>
                            </div>

                            <!-- Ciało karty -->
                            <div class="card-body d-flex flex-column">
                                @if ($menu->dishes->isEmpty())
                                    <div class="text-center text-muted py-3">
                                        <i class="bi bi-inbox display-6"></i>
                                        <p class="mt-2 mb-0"><em>Brak przypisanych dań</em></p>
                                    </div>
                                @else
                                    <div class="flex-grow-1">
                                        @foreach ($menu->dishesByType as $type => $dishes)
                                            <div class="mb-3">
                                                <h6 class="text-primary small fw-bold mb-2">
                                                    <i class="bi bi-caret-right"></i> {{ $type }}
                                                </h6>
                                                <p class="small text-muted mb-2 lh-sm">
                                                    {{ $dishes->pluck('name')->join(', ') }}
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Stopka karty z akcjami -->
                            <div class="card-footer bg-white border-0 pt-0">
                                <!-- Radio button do wyboru menu -->
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="menu_id"
                                        id="menu_{{ $menu->id }}" value="{{ $menu->id }}"
                                        @checked(old('menu_id') == $menu->id)>
                                    <label class="form-check-label small fw-medium" for="menu_{{ $menu->id }}">
                                        Wybierz to menu
                                    </label>
                                </div>

                                <!-- Przyciski akcji -->
                                <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                    <button type="button" class="btn btn-outline-primary btn-sm flex-fill me-md-1 mb-2 mb-md-0"
                                            data-bs-toggle="modal"
                                            data-bs-target="#menuDetailsModal{{ $menu->id }}">
                                        <i class="bi bi-eye"></i>
                                        <span class="d-none d-sm-inline">Szczegóły</span>
                                    </button>

                                    <a href="{{ route('menus.edit', ['menu' => $menu->id]) }}"
                                       class="btn btn-outline-info btn-sm flex-fill me-md-1 mb-2 mb-md-0">
                                        <i class="bi bi-pencil"></i>
                                        <span class="d-none d-sm-inline">Edytuj</span>
                                    </a>

                                    <form action="{{ route('menus.destroy', $menu->id) }}" method="POST"
                                        class="d-inline flex-fill">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                            onclick="return confirm('Na pewno chcesz usunąć to menu?')">
                                            <i class="bi bi-trash"></i>
                                            <span class="d-none d-sm-inline">Usuń</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('shared.modal', ['menu' => $menu])
                @endforeach
            </div>
        @endif
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

</body>

@include('shared.footer')om->name }} - {{ $room->capacity }} miejsc
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('rooms')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            @include('shared.menu-card')

            <div class="mt-3">
                <button type="submit" name="action" value="custom" class="btn btn-primary">
                    Utwórz własne menu
                </button>

                <button type="submit" name="action" value="event" class="btn btn-success">
                    Utwórz wydarzenie
                </button>
            </div>
        </form>
    </div>
</body>
