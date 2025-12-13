@include('shared.html')
@include('shared.head', ['pageTitle' => 'Dodaj nowe wydarzenie'])

<body>
    @include('shared.navbar')

    <div class="container-fluid mt-5 px-5">
        <h1>Dodaj nowe wydarzenie</h1>

        @guest
            <div class="alert alert-warning">
                Aby utworzyć wydarzenie, musisz się
                <a href="{{ route('login', ['redirect_to' => url()->current()]) }}" class="alert-link">zalogować</a>
                lub
                <a href="{{ route('register') }}" class="alert-link">zarejestrować</a>.
            </div>
        @endguest

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-3">Dostępność terminów</h5>
                <div id="calendar"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('events.store', ['id' => $restaurant->id]) }}">
            @csrf

            <fieldset @guest disabled @endguest>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="date" class="form-label">Data</label>
                        <input type="date" name="date" id="date" class="form-control"
                            value="{{ old('date') }}" required>
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
                            min="1" value="{{ old('number_of_people') }}" required>
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

                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @foreach ($rooms as $room)
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $room->name }}</h5>
                                        <ul class="list-group list-group-flush mb-3">
                                            <li class="list-group-item">
                                                <strong>Pojemność:</strong> {{ $room->capacity }} osób
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Opis:</strong>
                                                {{ $room->description ?? 'Brak opisu' }}
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">
                                            Cena sali: {{ $room->price }} zł
                                        </span>
                                        <div class="form-check">
                                            <input class="form-check-input room-checkbox" type="checkbox" name="rooms[]"
                                                id="room_{{ $room->id }}" value="{{ $room->id }}"
                                                data-room-id="{{ $room->id }}">
                                            <label class="form-check-label" for="room_{{ $room->id }}">
                                                Wybierz
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @include('shared.menu-card')

                @if ($restaurant->booking_regulations)
                    <div class="alert alert-light border mt-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                Zapoznałem się i akceptuję
                                <a href="#" data-bs-toggle="modal" data-bs-target="#bookingRegulationsModal" class="text-decoration-none fw-bold">
                                    regulamin rezerwacji
                                </a>
                                restauracji {{ $restaurant->name }}.
                            </label>
                        </div>
                    </div>
                @endif

                <div class="mt-4 mb-5">
                    @auth
                        <button type="submit" name="action" value="custom" class="btn btn-primary">
                            Utwórz własne menu
                        </button>
                        <button type="submit" name="action" value="event" class="btn btn-success">
                            Utwórz wydarzenie
                        </button>
                    @endauth

                    @guest
                         <button type="button" class="btn btn-secondary" disabled>
                            Zaloguj się, aby zarezerwować
                        </button>
                    @endguest
                </div>

            </fieldset>
        </form>
    </div>

    @if ($restaurant->booking_regulations)
        <div class="modal fade" id="bookingRegulationsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Regulamin rezerwacji - {{ $restaurant->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {!! nl2br(e($restaurant->booking_regulations)) !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="{{ asset('js/calendar.js') }}"></script>

    <script>
        window.busyRoomsUrl = "{{ route('events.busy-rooms') }}";
        window.calendarUrl = "{{ route('events.calendar', $restaurant->id) }}";
        window.restaurantId = "{{ $restaurant->id }}";

        document.addEventListener('DOMContentLoaded', function() {
            const modalEl = document.getElementById('bookingRegulationsModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        });
    </script>
</body>
