@include('shared.html')
@include('shared.head', ['pageTitle' => 'Porównanie zmian'])

<body>
    @include('shared.navbar')

    <div class="container-fluid mt-4 px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Porównanie wersji wydarzenia</h1>
            <a href="{{ route('users.user-dashboard') }}" class="btn btn-secondary">
                Wróć do wydarzeń
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between">
                <div class="w-50 text-center border-end">
                    Oryginalna wersja Użytkownika
                </div>
                <div class="w-50 text-center">
                    Wersja zmieniona przez Managera
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 align-top">
                        <thead class="table-light text-center align-middle">
                            <tr>
                                <th style="width: 50%;">Dane pierwotne</th>
                                <th style="width: 50%;">Dane po edycji</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $origPeople = $original['number_of_people'] ?? 'Brak danych';
                                $currPeople = $event->number_of_people;
                                $isDiff = $origPeople != $currPeople;
                            @endphp
                            <tr>
                                <td class="text-center text-muted align-middle">
                                    <small>Liczba osób</small><br>
                                    <span class="fs-5">{{ $origPeople }}</span>
                                </td>
                                <td class="text-center align-middle {{ $isDiff ? 'bg-warning bg-opacity-10' : '' }}">
                                    <small>Liczba osób</small><br>
                                    <span class="fs-5 fw-bold {{ $isDiff ? 'text-danger' : 'text-success' }}">
                                        {{ $currPeople }}
                                    </span>
                                </td>
                            </tr>

                            @php
                                $origStart = substr($original['start_time'] ?? '', 0, 5);
                                $origEnd = substr($original['end_time'] ?? '', 0, 5);
                                $currStart = substr($event->start_time, 0, 5);
                                $currEnd = substr($event->end_time, 0, 5);
                                $isTimeDiff = $origStart != $currStart || $origEnd != $currEnd;
                            @endphp
                            <tr>
                                <td class="text-center text-muted align-middle">
                                    <small>Godziny</small><br>
                                    {{ $origStart }} - {{ $origEnd }}
                                </td>
                                <td
                                    class="text-center align-middle {{ $isTimeDiff ? 'bg-warning bg-opacity-10' : '' }}">
                                    <small>Godziny</small><br>
                                    <span class="fw-bold {{ $isTimeDiff ? 'text-danger' : 'text-success' }}">
                                        {{ $currStart }} - {{ $currEnd }}
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td class="p-3 align-middle">
                                    <small class="text-muted d-block mb-2">Wybrane Sale:</small>
                                    @forelse($originalRoomNames as $name)
                                        <span class="badge bg-secondary mb-1">{{ $name }}</span>
                                    @empty
                                        <em class="text-muted">Brak sal</em>
                                    @endforelse
                                </td>
                                <td class="p-3 align-middle">
                                    <small class="text-muted d-block mb-2">Wybrane Sale:</small>
                                    @forelse($event->rooms as $room)
                                        @php
                                            $isNew = !$originalRoomNames->contains($room->name);
                                        @endphp
                                        <span class="badge {{ $isNew ? 'bg-success' : 'bg-secondary' }} mb-1">
                                            {{ $room->name }}
                                            @if ($isNew)
                                                <i class="bi bi-plus-circle ms-1"></i>
                                            @endif
                                        </span>
                                    @empty
                                        <em class="text-muted">Brak sal</em>
                                    @endforelse
                                </td>
                            </tr>

                            <tr>
                                <td class="p-0 bg-light align-top">
                                    <div class="p-3">
                                        <small class="text-muted d-block mb-2">Szczegóły Menu:</small>
                                        @if ($originalMenus->isNotEmpty())
                                            <div class="d-flex flex-column gap-3">
                                                @foreach ($originalMenus as $menu)
                                                    <div class="card border bg-white">
                                                        <div class="card-header py-1 px-2 bg-secondary text-white small d-flex justify-content-between align-items-center">
                                                            <span>
                                                                <strong>{{ $menu->name }}</strong>
                                                                ({{ number_format($menu->price, 2) }} zł)
                                                            </span>
                                                            <span class="badge bg-light text-dark rounded-pill">
                                                                {{ $original['number_of_people'] ?? '-' }} porcji
                                                            </span>
                                                        </div>
                                                        <div class="card-body p-2">
                                                            @foreach ($menu->dishesByType ?? [] as $type => $dishes)
                                                                <div class="mb-2">
                                                                    <strong class="text-muted x-small"
                                                                        style="font-size: 0.75rem;">{{ strtoupper($type) }}</strong>
                                                                    <ul
                                                                        class="list-unstyled mb-0 ps-2 border-start border-3">
                                                                        @foreach ($dishes as $dish)
                                                                            <li class="mb-1"
                                                                                style="font-size: 0.85rem;">
                                                                                <span
                                                                                    class="fw-semibold">{{ $dish->name }}</span>
                                                                                @if ($dish->description)
                                                                                    <br><span
                                                                                        class="text-muted fst-italic"
                                                                                        style="font-size: 0.75rem;">{{ $dish->description }}</span>
                                                                                @endif
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <em class="text-muted">Brak wyboru</em>
                                        @endif
                                    </div>
                                </td>

                                <td class="p-0 bg-light align-top">
                                    <div class="p-3">
                                        <small class="text-muted d-block mb-2">Szczegóły Menu:</small>
                                        @if ($event->menus->isNotEmpty())
                                            <div class="d-flex flex-column gap-3">
                                                @foreach ($event->menus as $menu)
                                                    @php
                                                        $isNewMenu = !$originalMenus->contains('id', $menu->id);
                                                    @endphp
                                                    <div
                                                        class="card border {{ $isNewMenu ? 'border-success' : 'bg-white' }}">
                                                        <div
                                                            class="card-header py-1 px-2 d-flex justify-content-between align-items-center {{ $isNewMenu ? 'bg-success text-white' : 'bg-primary text-white' }} small">
                                                            <span>
                                                                <strong>{{ $menu->name }}</strong>
                                                                ({{ number_format($menu->price, 2) }} zł)
                                                                @if ($isNewMenu)
                                                                    <span
                                                                        class="badge bg-white text-success ms-1">NOWE</span>
                                                                @endif
                                                            </span>
                                                            <span
                                                                class="badge bg-light text-dark rounded-pill">{{ $menu->pivot->amount }}
                                                                porcji</span>
                                                        </div>
                                                        <div class="card-body p-2 bg-white">
                                                            @foreach ($menu->dishesByType ?? [] as $type => $dishes)
                                                                <div class="mb-2">
                                                                    <strong class="text-muted x-small"
                                                                        style="font-size: 0.75rem;">{{ strtoupper($type) }}</strong>
                                                                    <ul
                                                                        class="list-unstyled mb-0 ps-2 border-start border-3 border-primary">
                                                                        @foreach ($dishes as $dish)
                                                                            <li class="mb-1"
                                                                                style="font-size: 0.85rem;">
                                                                                <span
                                                                                    class="fw-semibold">{{ $dish->name }}</span>
                                                                                @if ($dish->description)
                                                                                    <br><span
                                                                                        class="text-muted fst-italic"
                                                                                        style="font-size: 0.75rem;">{{ $dish->description }}</span>
                                                                                @endif
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <em class="text-muted">Brak menu</em>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @php
                                $descDiff = ($original['description'] ?? '') != $event->description;
                            @endphp
                            <tr>
                                <td class="p-3 align-middle">
                                    <small class="text-muted">Opis oryginalny:</small>
                                    <div class="fst-italic text-muted mt-1">{{ $original['description'] ?? '-' }}</div>
                                </td>
                                <td class="p-3 align-middle {{ $descDiff ? 'bg-warning bg-opacity-10' : '' }}">
                                    <small class="text-muted">Zmieniony opis:</small>
                                    <div class="mt-1 {{ $descDiff ? 'fw-bold' : '' }}">
                                        {{ $event->description }}
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
