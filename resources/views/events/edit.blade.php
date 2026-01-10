@include('shared.html')
@include('shared.head', ['pageTitle' => 'Edytuj dane wydarzenia'])

<body>
    @include('shared.navbar')

    <div class="container-fluid mt-5 px-5">
        <h1>Edytuj dane wydarzenia</h1>

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

        <form action="{{ route('events.update', $event->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Data</label>
                    <input type="date" name="date" id="date" class="form-control"
                        value="{{ old('date', $event->date) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Godzina rozpoczęcia</label>
                    <input type="time" name="start_time" id="start_time" class="form-control"
                        value="{{ old('start_time', \Carbon\Carbon::parse($event->start_time)->format('H:i')) }}"
                        required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Godzina zakończenia</label>
                    <input type="time" name="end_time" id="end_time" class="form-control"
                        value="{{ old('end_time', \Carbon\Carbon::parse($event->end_time)->format('H:i')) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Liczba osób</label>
                    <input type="number" name="number_of_people" class="form-control"
                        value="{{ old('number_of_people', $event->number_of_people) }}" min="1" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Typ wydarzenia</label>
                    <select name="event_type_id" class="form-select" required>
                        @foreach ($eventTypes as $type)
                            <option value="{{ $type->id }}" @selected(old('event_type_id', $event->event_type_id) == $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Opis</label>
                <textarea name="description" class="form-control" rows="3" required>{{ old('description', $event->description) }}</textarea>
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
                                        <input class="form-check-input" type="checkbox" name="rooms[]"
                                            value="{{ $room->id }}" @checked(in_array($room->id, old('rooms', $event->rooms->pluck('id')->toArray())))>

                                        <label class="form-check-label">
                                            Wybierz
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @foreach ($event->menus as $menu)
                <input type="hidden" name="menus_id[]" value="{{ $menu->id }}">
            @endforeach

            <div class="mt-3 mb-5">
                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                <a href="{{ route('users.user-dashboard') }}" class="btn btn-secondary">Anuluj</a>
            </div>
        </form>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
        window.calendarUrl = "{{ route('events.calendar', $event->restaurant_id) }}";
        window.busyRoomsUrl = "{{ route('events.busy-rooms') }}";
        window.restaurantId = "{{ $event->restaurant_id }}";
        window.eventId = "{{ $event->id }}";
    </script>

    <script src="{{ asset('js/calendar.js') }}"></script>
    <script src="{{ asset('js/edit_reservation.js') }}"></script>

</body>
