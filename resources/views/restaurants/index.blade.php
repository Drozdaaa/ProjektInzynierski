@include('shared.html')
@include('shared.head', ['pageTitle' => 'Szczegóły restauracji'])

@include('shared.navbar')

<body>
    <div class="container-fluid mt-5 px-5">
        <h1>Moja restauracja</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card mt-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Podstawowe informacje</span>
                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal"
                    data-bs-target="#editRestaurantModal">
                    Edytuj dane
                </button>
            </div>
            <div class="card-body">
                <h4 class="card-title">{{ $restaurant->name }}</h4>
                <p class="card-text">{{ $restaurant->description ?? '-' }}</p>
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
            <div class="card-header bg-success text-white">
                Sale
            </div>
            <div class="card-body">
                @if ($restaurant->rooms->isEmpty())
                    <p>Brak dodanych sal.</p>
                @else
                    <table class="table table-striped align-middle text-center">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nazwa sali</th>
                                <th scope="col">Pojemność</th>
                                <th scope="col">Opis</th>
                                <th scope="col">Dostępność</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($restaurant->rooms as $room)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $room->name }}</td>
                                    <td>{{ $room->capacity }}</td>
                                    <td>{{ $room->description ?? '-' }}</td>
                                    <td>{{ $room->is_available ? 'Dostępna' : 'Niedostępna' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="editRestaurantModal" tabindex="-1" aria-labelledby="editRestaurantModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('restaurants.update', $restaurant->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRestaurantModalLabel">Edytuj dane restauracji i adres</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Dane restauracji</h6>

                            <div class="mb-3">
                                <label for="name" class="form-label">Nazwa restauracji</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $restaurant->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Opis</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="3">{{ old('description', $restaurant->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Adres</h6>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="street" class="form-label">Ulica</label>
                                        <input type="text" name="street" id="street"
                                            class="form-control @error('street') is-invalid @enderror"
                                            value="{{ old('street', $restaurant->address->street) }}" required>
                                        @error('street')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
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
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="city" class="form-label">Miasto</label>
                                        <input type="text" name="city" id="city"
                                            class="form-control @error('city') is-invalid @enderror"
                                            value="{{ old('city', $restaurant->address->city) }}" required>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="postal_code" class="form-label">Kod pocztowy</label>
                                        <input type="text" name="postal_code" id="postal_code"
                                            class="form-control @error('postal_code') is-invalid @enderror"
                                            value="{{ old('postal_code', $restaurant->address->postal_code) }}"
                                            required>
                                        @error('postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-primary">Zapisz wszystkie zmiany</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('editRestaurantModal'));
            modal.show();
        });
    </script>
@endif
