<div id="table-events" class="d-none">
    <div class="card mb-4 bg-light">
        <div class="card-body">
            <form action="{{ route('users.admin-dashboard') }}" method="GET" class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label fw-bold">Restauracja</label>
                    <select name="event_restaurant_id" id="filter_restaurant_id" class="form-select">
                        <option value="">Wszystkie</option>
                        @foreach ($filter_restaurants as $res)
                            <option value="{{ $res->id }}" @selected(request('event_restaurant_id') == $res->id)>
                                {{ $res->name }} ({{ $res->address->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status_id" class="form-select">
                        <option value="">Wszystkie</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}" @selected(request('status_id') == $status->id)>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Data od</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Data do</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"
                        onclick="localStorage.setItem('adminTab', 'events')">
                        Filtruj
                    </button>
                    <a href="{{ route('users.admin-dashboard') }}" class="btn btn-outline-secondary"
                        onclick="localStorage.setItem('adminTab', 'events')">
                        Wyczyść
                    </a>
                </div>
            </form>
        </div>
    </div>
    <div class="table-responsive-sm">
        <table class="table table-striped align-middle text-center">
            <thead class="table-primary">
                <tr>
                    <th scope="col">Data</th>
                    <th scope="col">Godziny</th>
                    <th scope="col">Lokal</th>
                    <th scope="col">Klient</th>
                    <th scope="col">Typ</th>
                    <th scope="col" style="width: 15%;">Status</th>
                    <th scope="col">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                    <tr>
                        <td>{{ $event->date }}</td>
                        <td>{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</td>
                        <td>{{ $event->restaurant->name }}</td>
                        <td>
                            {{ $event->user->first_name }} {{ $event->user->last_name }}<br>
                            <small class="text-muted">{{ $event->user->email }}</small>
                        </td>
                        <td>{{ $event->eventType->name }}</td>
                        <td>
                            <form action="{{ route('events.update-status', $event->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="status_id"
                                    class="form-select form-select-sm {{ $event->status_id == 1 ? 'border-warning' : ($event->status_id == 2 ? 'border-success' : 'border-secondary') }}"
                                    onchange="localStorage.setItem('adminTab', 'events'); this.form.submit()">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}" @selected($event->status_id == $status->id)>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <a href="{{ route('events.edit', $event->id) }}" class="btn btn-info btn-sm">
                                    Edytuj
                                </a>

                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Czy na pewno usunąć to wydarzenie?')">
                                        Usuń
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <th scope="row" colspan="7" class="text-center">Brak wydarzeń spełniających kryteria.</th>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $events->links('pagination::bootstrap-5') }}
    </div>
</div>
