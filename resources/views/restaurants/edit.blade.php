@include('shared.html')
@include('shared.head', ['pageTitle' => 'Edytuj lokal'])

<body>
    @include('shared.navbar')
    <div class="container-fluid mt-5 px-5">
        <h1>Edytuj dane lokalu</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
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

        <form method="POST" action="{{ route('restaurants.update', $restaurant->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Nazwa restauracji</label>
                <input type="text" name="name" id="name" class="form-control"
                    value="{{ old('name', $restaurant->name) }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Opis</label>
                <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $restaurant->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="user_email" class="form-label">E-mail właściciela</label>
                <input type="email" name="user_email" id="user_email" class="form-control"
                    value="{{ old('user_email', $restaurant->user->email ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="street" class="form-label">Ulica</label>
                <input type="text" name="street" id="street" class="form-control"
                    value="{{ old('street', $restaurant->address->street ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">Miasto</label>
                <input type="text" name="city" id="city" class="form-control"
                    value="{{ old('city', $restaurant->address->city ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="postal_code" class="form-label">Kod pocztowy</label>
                <input type="text" name="postal_code" id="postal_code" class="form-control"
                    value="{{ old('postal_code', $restaurant->address->postal_code ?? '') }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
            <a href="{{ route('users.admin-dashboard') }}" class="btn btn-secondary">Anuluj</a>
        </form>
    </div>
</body>
