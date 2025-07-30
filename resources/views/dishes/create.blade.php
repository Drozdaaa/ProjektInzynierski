@include('shared.html')
@include('shared.head', ['pageTitle' => 'Dodaj nowe danie'])

<body>
    @include('shared.navbar')
    <div class="container mt-5">
        <h2>Dodaj nowe danie</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('dishes.store', ['id' => $restaurant->id]) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nazwa dania</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Opis</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Cena (zł)</label>
                <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" step="0.01" min="0" required>
            </div>

            <div class="mb-3">
                <label for="dish_type_id" class="form-label">Typ dania</label>
                <select name="dish_type_id" id="dish_type_id" class="form-select" required>
                    @foreach($dishTypes as $dishType)
                        <option value="{{ $dishType->id }}" {{ old('dish_type_id') == $dishType->id ? 'selected' : '' }}>
                            {{ $dishType->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Dodaj danie</button>
            <a href="{{ route('users.manager-dashboard') }}" class="btn btn-secondary">Anuluj</a>
        </form>
    </div>
</body>
