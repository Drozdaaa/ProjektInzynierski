@include('shared.html')
@include('shared.head', ['pageTitle' => 'Panel Menadżera'])

<body>
    @include('shared.navbar')

    <div class="container-fluid mt-5 px-5">
        <h1 class="mb-4">Panel Menadżera</h1>

        <div class="d-flex justify-content-between align-items-center mb-4">
            @if ($restaurant)
                <a href="{{ route('events.create', ['id' => $restaurant->id]) }}" class="btn btn-primary">
                    Dodaj wydarzenie
                </a>
            @else
                <div class="alert alert-warning mb-0">
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

        <div class="table-responsive">
            <table class="table table-striped align-middle text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Imię i nazwisko</th>
                        <th>Numer telefonu</th>
                        <th>Email</th>
                        <th>Rodzaj wydarzenia</th>
                        <th>Data</th>
                        <th>Liczba osób</th>
                        <th>Opis</th>
                        <th>Status</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr data-status="{{ $event->status->name }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $event->user->first_name }} {{ $event->user->last_name }}</td>
                            <td>{{ $event->user->phone }}</td>
                            <td>{{ $event->user->email }}</td>
                            <td>{{ $event->eventType->name }}</td>
                            <td>{{ $event->date }}</td>
                            <td>{{ $event->number_of_people }}</td>
                            <td>{{ $event->description }}</td>
                            <td>
                                <span
                                    class="badge
                                    @if ($event->status->name === 'Oczekujące') bg-warning
                                    @elseif($event->status->name === 'Zaplanowane') bg-info
                                    @elseif($event->status->name === 'Zakończone') bg-success
                                    @else bg-secondary @endif">
                                    {{ $event->status->name }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('menus.show', $event->id) }}" class="btn btn-sm btn-primary">
                                    Zobacz menu
                                </a>

                                @if ($event->status->name === 'Zaplanowane')
                                    <a href="{{ route('events.edit', $event->id) }}" class="btn btn-sm btn-info">
                                        Edytuj
                                    </a>
                                    <form action="{{ route('events.update-status', $event->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status_id" value="3">
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Na pewno archiwizować to wydarzenie?')">
                                            Archiwizuj
                                        </button>
                                    </form>
                                @elseif ($event->status->name === 'Oczekujące')
                                    <form action="{{ route('events.update-status', $event->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status_id" value="2">
                                        <button type="submit" class="btn btn-sm btn-success"
                                            onclick="return confirm('Na pewno zaplanować to wydarzenie?')">
                                            Zaplanuj
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Na pewno usunąć to wydarzenie?')">
                                            Usuń
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                Brak wydarzeń.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
