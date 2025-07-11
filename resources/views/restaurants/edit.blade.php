@include('shared.html')
@include('shared.head', ['pageTitle' => 'Panel Administratora'])

<body>
    @include('shared.navbar')
    <div class="container mt-5">
    <h2>Edytuj restaurację</h2>

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
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $restaurant->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Opis</label>
            <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $restaurant->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="user_id" class="form-label">Właściciel (użytkownik)</label>
            <select name="user_id" id="user_id" class="form-select" required>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $restaurant->user_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
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
