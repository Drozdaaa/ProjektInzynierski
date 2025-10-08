@include('shared.html')
@include('shared.head', ['pageTitle' => 'Moje wydarzenia'])

<body>
    @include('shared.navbar')

    <div class="container-fluid mt-5 px-5">
        <h1>Moje wydarzenia</h1>
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
        <div class="table-responsive-sm">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Rodzaj wydarzenia</th>
                        <th scope="col">Data</th>
                        <th scope="col">Liczba osób</th>
                        <th scope="col">Opis</th>
                        <th scope="col">Koszt</th>
                        <th scope="col">Status</th>
                        <th scope="col">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr data-status="{{ $event->status->name }}">
                            <td>{{ $event->eventType->name }}</td>
                            <td>{{ $event->date }}</td>
                            <td>{{ $event->number_of_people }}</td>
                            <td>{{ $event->description }}</td>

                            <td>
                                @if ($event->menu)
                                    {{ $event->number_of_people * $event->menu->price }} zł
                                @else
                                    <span class="text-muted">Brak menu</span>
                                @endif
                            </td>

                            <td>{{ $event->status->name }}</td>

                            <td>
                                @if (!$event->menu)
                                    <a href="{{ route('menus.user-create', ['restaurant' => $event->restaurant->id, 'event' => $event->id]) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        Dodaj menu
                                    </a>
                                @else
                                    <a href="{{ route('events.show', ['restaurant' => $event->restaurant->id, 'event' => $event->id]) }}"
                                        class="btn btn-sm btn-outline-success">
                                        Zobacz
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Brak wydarzeń.</td>
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
