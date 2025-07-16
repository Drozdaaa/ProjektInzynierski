@include('shared.html')
@include('shared.head', ['pageTitle' => 'Panel Menadżera'])

<body>
    @include('shared.navbar')

    <h1>Panel Menadżera</h1>
    <div class = 'container-fluid px-5'>
        <div class="btn-group mt-3" role="group" aria-label="Status filtr">
            <input type="radio" class="btn-check" name="btnstatus" id="btn-all" autocomplete="off" checked
                onclick="filterStatus('all')">
            <label class="btn btn-outline-primary" for="btn-all">Wszystkie</label>

            <input type="radio" class="btn-check" name="btnstatus" id="btn-zaplanowane" autocomplete="off"
                onclick="filterStatus('Zaplanowane')">
            <label class="btn btn-outline-primary" for="btn-zaplanowane">Zaplanowane</label>

            <input type="radio" class="btn-check" name="btnstatus" id="btn-zakonczone" autocomplete="off"
                onclick="filterStatus('Zakończone')">
            <label class="btn btn-outline-primary" for="btn-zakonczone">Zakończone</label>
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

                                    <a href="{{route('events.edit', $event->id)}}" class="btn btn-info">Edytuj</a>

                                        <form action="{{ route('events.archive', $event->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('Na pewno archiwizować to wydarzenie?')">
                                                Archiwizuj
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('event.destroy', $event->id) }}" method="POST"
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
