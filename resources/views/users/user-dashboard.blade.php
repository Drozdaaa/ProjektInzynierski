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
                                    {{ $event->restaurant->address->building_number }},
                                    {{ $event->restaurant->address->postal_code }}
                                </small>
                            </td>
                            <td>{{ $event->eventType->name }}</td>
                            <td>
                                {{ $event->date }}<br>
                                <small class="text-muted">{{ $event->start_time }} - {{ $event->end_time }}</small>
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
                            <td>{{ $event->description }}</td>
                            <td>
                                @if ($event->menus->isNotEmpty())
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#menuDetailsModal{{ $event->id }}">
                                        Szczegóły
                                    </button>

                                    @include('shared.modal', ['event' => $event])

                                    @if ($event->status->name === 'Oczekujące')
                                        <a href="{{ route('menus.user.edit', ['event' => $event->id, 'menu' => $event->menus->first()->id]) }}"
                                            class="btn btn-sm btn-info">
                                            Edytuj menu
                                        </a>
                                    @endif
                                @else
                                    @if ($event->status->name === 'Oczekujące')
                                        <a href="{{ route('menus.user-create', ['restaurant' => $event->restaurant_id, 'event' => $event->id]) }}"
                                            class="btn btn-sm btn-success">
                                            Utwórz menu
                                        </a>
                                    @endif
                                @endif
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
                            <td colspan="10" class="text-center text-muted py-4">Brak wydarzeń.</td>
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
