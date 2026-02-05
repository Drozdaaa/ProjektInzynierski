@include('shared.html')
@include('shared.head', ['pageTitle' => 'Moje wydarzenia'])

<body>
    @include('shared.navbar')
    <div class="container-fluid mt-5 px-5">
        <h1 class="mb-4">Moje wydarzenia</h1>
        <div class="d-grid d-sm-flex gap-2 justify-content flex-sm-wrap mb-4" role="group" aria-label="Status filtr">
            @foreach (['all' => 'Wszystkie', 'Oczekujące' => 'Oczekujące', 'Zaplanowane' => 'Zaplanowane', 'Zakończone' => 'Zakończone'] as $id => $label)
                <div class="flex">
                    <input type="radio" class="btn-check" name="btnstatus" id="btn-{{ $id }}" autocomplete="off"
                        onclick="applyFilter('{{ $id }}')" {{ $status === $id ? 'checked' : '' }}>

                    <label class="btn btn-outline-primary w-100 text-center"
                        for="btn-{{ $id }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Lokal</th>
                        <th>Rodzaj wydarzenia</th>
                        <th>Data</th>
                        <th>Sala</th>
                        <th>Liczba osób</th>
                        <th>Koszt</th>
                        <th>Opis</th>
                        <th>Menu</th>
                        <th>Akcje</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr data-status="{{ $event->status->name }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $event->restaurant->name }}<br>
                                <small class="text-muted">
                                    {{ $event->restaurant->address->city }},
                                    {{ $event->restaurant->address->street }}
                                    {{ $event->restaurant->address->building_number }}<br>
                                    {{ $event->manager->email }}<br>
                                    nr telefonu: {{ $event->manager->phone }}
                                </small>
                            </td>
                            <td>{{ $event->eventType->name }}</td>
                            <td>
                                {{ $event->date }}<br>
                                <small
                                    class="text-muted">{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</small>
                            </td>
                            <td>
                                {{ $event->rooms->pluck('name')->join(', ') }}
                                <br>
                                <small class="text-muted">
                                    {{ $event->rooms->sum('price') }} zł
                                </small>
                            </td>
                            <td>{{ $event->number_of_people }}</td>
                            <td>{{ number_format($event->total_cost, 2, '.', '') }} zł</td>
                            <td class="description">{{ $event->description }}</td>
                            <td>
                                @if ($event->menus->isNotEmpty())
                                    <div class="d-flex flex-column gap-1">
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#menuDetailsModal{{ $event->id }}">
                                            Szczegóły
                                        </button>
                                    </div>

                                    @include('shared.modal', ['event' => $event])
                                @else
                                    @if (Gate::allows('manage-event', $event))
                                        <div class="d-flex flex-column gap-1">
                                            <a href="{{ route('menus.user-create', ['restaurant' => $event->restaurant_id, 'event' => $event->id]) }}"
                                                class="btn btn-sm btn-success">
                                                Utwórz menu
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1 align-items-center">
                                    @if ($event->original_data)
                                        <a href="{{ route('events.compare', $event->id) }}"
                                            class="btn btn-sm btn-outline-secondary text-nowrap w-100"
                                            title="Porównaj zmiany z oryginałem"> Zmiany
                                        </a>
                                    @endif

                                    @if (Gate::allows('manage-event', $event))
                                        <a href="{{ route('events.edit', $event->id) }}"
                                            class="btn btn-sm btn-warning w-100 text-nowrap">
                                            Edytuj
                                        </a>
                                    @endif
                                    @if (Gate::allows('manage-event', $event))
                                        <form action="{{ route('events.destroy', $event->id) }}" method="POST"
                                            onsubmit="return confirm('Czy na pewno chcesz usunąć to wydarzenie?');"
                                            class="w-100">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger w-100 text-nowrap">
                                                Usuń
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>

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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">Brak wydarzeń.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $events->links() }}
    </div>
    <script src="{{ asset('js/filter.js') }}"></script>
</body>
