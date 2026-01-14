@include('shared.html')
@include('shared.head', ['pageTitle' => 'Szczegóły restauracji'])

@include('shared.navbar')

<div class="container-fluid mt-5 px-5">
    <h1>Moja restauracja</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="card mt-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span>Podstawowe informacje</span>
            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal"
                data-bs-target="#editRestaurantModal">Edytuj dane</button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    @if ($restaurant->image)
                        <img src="{{ asset('storage/' . $restaurant->image) }}" class="img-fluid rounded shadow-sm"
                            alt="{{ $restaurant->name }}" style="object-fit: cover; width: 100%; max-height: 300px;">
                    @else
                        <div class="rounded shadow-sm bg-light d-flex align-items-center justify-content-center"
                            style="height: 300px; background-color: #f0f0f0; width: 100%;">
                            <span class="text-muted">Brak zdjęcia</span>
                        </div>
                    @endif
                </div>

                <div class="col-md-8">
                    <h4 class="card-title">{{ $restaurant->name }}</h4>
                    <p class="card-text">{{ $restaurant->description ?? '-' }}</p>
                    <hr>
                    <strong>Regulamin rezerwacji</strong><br>
                    <div class="text-muted small mt-2">
                        {!! nl2br(e($restaurant->booking_regulations ?? 'Brak regulaminu')) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <span>Adres</span>
        </div>
        <div class="card-body">
            <ul>
                <li>Ulica: {{ $restaurant->address->street }} {{ $restaurant->address->building_number }}</li>
                <li>Miasto: {{ $restaurant->address->city }}</li>
                <li>Kod pocztowy: {{ $restaurant->address->postal_code }}</li>
            </ul>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <span>Sale</span>
            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#roomModal"
                onclick="openRoomModal('add')">Dodaj salę</button>
        </div>
        <div class="card-body">
            @if ($restaurant->rooms->isEmpty())
                <p>Brak dodanych sal.</p>
            @else
                <table class="table table-striped align-middle text-center">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Nazwa sali</th>
                            <th>Cena</th>
                            <th>Pojemność</th>
                            <th>Opis</th>
                            <th>Czas sprzątania</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($restaurant->rooms as $room)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $room->name }}</td>
                                <td>{{ $room->price }} zł</td>
                                <td>{{ $room->capacity }}</td>
                                <td>{{ $room->description ?? '-' }}</td>
                                <td>
                                    @php
                                        $duration = $room->cleaning_duration ?? 0;
                                        $hours = floor($duration / 60);
                                        $minutes = $duration % 60;
                                    @endphp

                                    @if ($duration == 0)
                                        -
                                    @else
                                        @if ($hours > 0)
                                            {{ $hours }} godz.
                                        @endif
                                        @if ($minutes > 0)
                                            {{ $minutes }}min.
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm gap-1">
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#roomModal"
                                            onclick="openRoomModal('edit', {{ $room->id }}, '{{ $room->name }}', {{ $room->capacity }}, {{ $room->price }}, '{{ $room->description }}', {{ $room->cleaning_duration ?? 0 }})">
                                            Edytuj
                                        </button>
                                        <form action="{{ route('rooms.destroy', [$restaurant->id, $room->id]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Na pewno usunąć salę?')">Usuń</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="editRestaurantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('restaurants.update', $restaurant->id) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" value="restaurant_edit">
                <div class="modal-header">
                    <h5 class="modal-title">Edytuj dane restauracji i adres</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <h6>Dane restauracji</h6>
                        <div class="mb-3">
                            <label for="image" class="form-label">Zdjęcie lokalu</label>
                            <input type="file" name="image" id="image"
                                class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            <div class="form-text">Pozostaw puste, jeśli nie chcesz zmieniać obecnego zdjęcia.</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="restaurant_name" class="form-label">Nazwa restauracji</label>
                            <input type="text" name="name" id="restaurant_name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $restaurant->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="restaurant_description" class="form-label">Opis</label>
                            <textarea name="description" id="restaurant_description" class="form-control @error('description') is-invalid @enderror"
                                rows="3">{{ old('description', $restaurant->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="booking_regulations" class="form-label">Regulamin rezerwacji</label>
                            <textarea name="booking_regulations" id="booking_regulations"
                                class="form-control @error('booking_regulations') is-invalid @enderror" rows="5"
                                placeholder="Wpisz regulamin rezerwacji">{{ old('booking_regulations', $restaurant->booking_regulations) }}</textarea>
                            @error('booking_regulations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6>Adres</h6>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="street" class="form-label">Ulica</label>
                                <input type="text" name="street" id="street"
                                    class="form-control @error('street') is-invalid @enderror"
                                    value="{{ old('street', $restaurant->address->street) }}" required>
                                @error('street')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="building_number" class="form-label">Numer budynku</label>
                                <input type="number" name="building_number" id="building_number"
                                    class="form-control @error('building_number') is-invalid @enderror"
                                    value="{{ old('building_number', $restaurant->address->building_number) }}"
                                    required>
                                @error('building_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">Miasto</label>
                                <input type="text" name="city" id="city"
                                    class="form-control @error('city') is-invalid @enderror"
                                    value="{{ old('city', $restaurant->address->city) }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="postal_code" class="form-label">Kod pocztowy</label>
                                <input type="text" name="postal_code" id="postal_code"
                                    class="form-control @error('postal_code') is-invalid @enderror"
                                    value="{{ old('postal_code', $restaurant->address->postal_code) }}" required>
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="roomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="roomForm" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="room_action">
                <div class="modal-header">
                    <h5 class="modal-title" id="roomModalLabel">Dodaj salę</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_method" id="roomFormMethod" value="POST">
                    <div class="mb-3">
                        <label for="room_name" class="form-label">Nazwa sali</label>
                        <input type="text" name="name" id="room_name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                            required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="room_capacity" class="form-label">Pojemność</label>
                        <input type="number" name="capacity" id="room_capacity"
                            class="form-control @error('capacity') is-invalid @enderror"
                            value="{{ old('capacity') }}" min="1" required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="room_price" class="form-label">Cena za wynajem (zł)</label>
                        <input type="number" name="price" id="room_price"
                            class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}"
                            step="0.01" min="0" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Czas potrzebny na sprzątanie (po wydarzeniu)</label>
                        <div class="row">
                            <div class="col-6">
                                <div class="input-group">
                                    <input type="number" name="cleaning_hours" id="room_cleaning_hours"
                                        class="form-control" value="{{ old('cleaning_hours', 0) }}" min="0"
                                        placeholder="0">
                                    <span class="input-group-text">Godz.</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group">
                                    <input type="number" name="cleaning_minutes" id="room_cleaning_minutes"
                                        class="form-control" value="{{ old('cleaning_minutes', 0) }}" min="0"
                                        max="59" placeholder="0">
                                    <span class="input-group-text">Min.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="room_description" class="form-label">Opis</label>
                        <textarea name="description" id="room_description" class="form-control @error('description') is-invalid @enderror"
                            rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-primary" id="roomSubmitBtn">Zapisz</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('js/postal_code.js') }}"></script>
<script>
    window.restaurantConfig = {
        routes: {
            store: "{{ route('rooms.store', $restaurant->id) }}",
            updateBase: "{{ url('/restaurants/' . $restaurant->id . '/rooms') }}"
        },
        oldInput: {
            name: @json(old('name')),
            capacity: @json(old('capacity')),
            price: @json(old('price')),
            description: @json(old('description')),
            cleaning_hours: @json(old('cleaning_hours')),
            cleaning_minutes: @json(old('cleaning_minutes'))
        },
        errors: {
            any: {{ $errors->any() ? 'true' : 'false' }},
            formType: "{{ old('form_type') }}"
        }
    };
</script>
<script src="{{ asset('js/restaurant.js') }}"></script>
