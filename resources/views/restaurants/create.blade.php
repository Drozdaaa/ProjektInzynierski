@include('shared.html')
@include('shared.head', ['pageTitle' => 'Dodaj restaurację'])

<body>
    @include('shared.navbar')
    <div class="container mt-5">
        <h2>Dodaj nową restaurację</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Wystąpiły błędy:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('restaurants.store') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nazwa restauracji</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Opis</label>
                <textarea name="description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="street" class="form-label">Ulica</label>
                <input type="text" name="street" id="street" class="form-control" value="{{ old('street') }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="building_number" class="form-label">Numer budynku</label>
                <input type="number" name="building_number" id="building_number" class="form-control"
                    value="{{ old('building_number') }}" required>
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">Miasto</label>
                <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="postal_code" class="form-label">Kod pocztowy</label>
                <input type="text" name="postal_code" id="postal_code" class="form-control"
                    value="{{ old('postal_code') }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Dodaj restaurację</button>
            <a href="{{ route('users.manager-dashboard') }}" class="btn btn-secondary">Anuluj</a>
        </form>
    </div>
</body>
