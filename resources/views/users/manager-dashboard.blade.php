@include('shared.html')
@include('shared.head', ['pageTitle' => 'Panel Menadżera'])

<body>
    @include('shared.navbar')

    <h1>Panel Menadżera</h1>
    <div class="container-fluid px-5">
        <div class="d-flex justify-content-between align-items-center mt-3">

            @if ($restaurant)
                <a href="{{ route('events.create', ['id' => $restaurant->id]) }}" class="btn btn-primary">
                    Dodaj wydarzenie
                </a>
            @else
                <div class="alert alert-warning">
                    Najpierw utwórz swoją restaurację, aby móc dodawać wydarzenia.
                    <a href="{{ route('restaurants.create') }}" class="alert-link">Utwórz restaurację</a>
                </div>
            @endif
            <div class="btn-group" role="group" aria-label="Status filtr">
                <input type="radio" class="btn-check" name="btnstatus" id="btn-all" autocomplete="off" checked
                    onclick="filterStatus('all')">
                <label class="btn btn-outline-primary" for="btn-all">Wszystkie</label>

                <input type="radio" class="btn-check" name="btnstatus" id="btn-oczekujące" autocomplete="off"
                    onclick="filterStatus('Oczekujące')">
                <label class="btn btn-outline-primary" for="btn-oczekujące">Oczekujące</label>

                <input type="radio" class="btn-check" name="btnstatus" id="btn-zaplanowane" autocomplete="off"
                    onclick="filterStatus('Zaplanowane')">
                <label class="btn btn-outline-primary" for="btn-zaplanowane">Zaplanowane</label>

                <input type="radio" class="btn-check" name="btnstatus" id="btn-zakonczone" autocomplete="off"
                    onclick="filterStatus('Zakończone')">
                <label class="btn btn-outline-primary" for="btn-zakonczone">Zakończone</label>
            </div>
        </div>
        <div class="table-responsive-sm">
            <div id="events">
                <table class="table table-events table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Imię i nazwisko</th>
                            <th scope="col">Numer telefonu</th>
                            <th scope="col">Email</th>
                            <th scope="col">Rodzaj wydarzenia</th>
                            <th scope="col">Data</th>
                            <th scope="col">Liczba osób</th>
                            <th scope="col">Opis</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $event)
                            <tr data-status="{{ $event->status->name }}">
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $event->user->first_name }} {{ $event->user->last_name }}</td>
                                <td>{{ $event->user->phone }}</td>
                                <td>{{ $event->user->email }}</td>
                                <td>{{ $event->eventType->name }}</td>
                                <td>{{ $event->date }}</td>
                                <td>{{ $event->number_of_people }}</td>
                                <td>{{ $event->description }}</td>
                                <td>{{ $event->status->name }}</td>
                                <td>
                                    <a href="{{ route('menus.show', $event->id) }}" class="btn btn-primary">Zobacz
                                        menu</a>
                                    @if ($event->status->name === 'Zaplanowane')
                                        <a href="{{ route('events.edit', $event->id) }}"
                                            class="btn btn-info">Edytuj</a>

                                        <form action="{{ route('events.update-status', $event->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status_id" value="3">
                                            <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('Na pewno archiwizować to wydarzenie?')">
                                                Archiwizuj
                                            </button>
                                        </form>
                                    @elseif ($event->status->name === 'Oczekujące')
                                    <form action="{{ route('events.update-status', $event->id)}}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status_id" value="2">
                                        <button type="submit" class="btn btn-primary"
                                                onclick="return confirm('Na pewno zaplanować to wydarzenie?')">
                                                Zaplanuj
                                            </button>
                                    @else
                                        <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('Na pewno usunąć to wydarzenie?')">
                                                Usuń
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <th scope="row" colspan="10" class="text-center">Brak wydarzeń.</th>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        function filterStatus(status) {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                row.style.display = (status === 'all' || rowStatus === status) ? '' : 'none';
            });
        }
    </script>
</body>
