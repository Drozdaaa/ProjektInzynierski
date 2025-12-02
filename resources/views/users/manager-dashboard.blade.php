@include('shared.html')
@include('shared.head', ['pageTitle' => 'Panel Menadżera'])

<body>
    @include('shared.navbar')

    <div class="container-fluid mt-5 px-5">
        <h1 class="mb-4">Panel Menadżera</h1>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div class="text-center text-md-start">
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
            </div>

            <div class="d-grid d-sm-flex gap-2 justify-content-center flex-sm-wrap" style="min-width: 300px;"
                role="group" aria-label="Status filtr">

                <div class="flex-fill">
                    <input type="radio" class="btn-check" name="btnstatus" id="btn-all" autocomplete="off"
                        onclick="applyFilter('all')" {{ $status === 'all' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary w-100 text-center" for="btn-all">
                        Wszystkie
                    </label>
                </div>

                <div class="flex-fill">
                    <input type="radio" class="btn-check" name="btnstatus" id="btn-oczekujące" autocomplete="off"
                        onclick="applyFilter('Oczekujące')" {{ $status === 'Oczekujące' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary w-100 text-center" for="btn-oczekujące">
                        Oczekujące
                    </label>
                </div>

                <div class="flex-fill">
                    <input type="radio" class="btn-check" name="btnstatus" id="btn-zaplanowane" autocomplete="off"
                        onclick="applyFilter('Zaplanowane')" {{ $status === 'Zaplanowane' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary w-100 text-center" for="btn-zaplanowane">
                        Zaplanowane
                    </label>
                </div>

                <div class="flex-fill">
                    <input type="radio" class="btn-check" name="btnstatus" id="btn-zakonczone" autocomplete="off"
                        onclick="applyFilter('Zakończone')" {{ $status === 'Zakończone' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary w-100 text-center" for="btn-zakonczone">
                        Zakończone
                    </label>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Klient</th>
                        <th>Rodzaj wydarzenia</th>
                        <th>Data</th>
                        <th>Sala</th>
                        <th>Liczba osób</th>
                        <th>Opis</th>
                        <th>Status</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <strong>{{ $event->user->first_name }} {{ $event->user->last_name }}</strong><br>
                                <small class="text-muted">{{ $event->user->email }}</small><br>
                                <small class="text-muted">nr telefonu: {{ $event->user->phone }}</small>
                            </td>
                            <td>{{ $event->eventType->name }}</td>
                            <td>
                                {{ $event->date }}<br>
                                <small class="text-muted">{{ $event->start_time }} - {{ $event->end_time }}</small>
                            </td>
                            <td>{{ $event->rooms->pluck('name')->join(', ') }}</td>
                            <td>{{ $event->number_of_people }}</td>
                            <td class="description">{{ $event->description }}</td>
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
                            <td>
                                @if ($event->menus->isNotEmpty())
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#menuDetailsModal{{ $event->id }}">
                                        Szczegóły
                                    </button>

                                    @include('shared.modal', ['event' => $event])
                                @else
                                    <strong><span class="text-muted">Brak menu</span></strong>
                                @endif

                                @if ($event->status->name === 'Zaplanowane')
                                    <a href="{{ route('events.edit', $event->id) }}"
                                        class="btn btn-sm btn-info">Edytuj</a>

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

                                    <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Na pewno odrzucić to wydarzenie?')">
                                            Odrzuć
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
                            <td colspan="11" class="text-center text-muted py-4">
                                Brak wydarzeń.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $events->appends(['status' => $status])->links() }}
        </div>
    </div>
    <script src="{{ asset('js/filter.js') }}"></script>
</body>
