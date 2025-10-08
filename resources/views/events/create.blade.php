@include('shared.html')
@include('shared.head', ['pageTitle' => 'Dodaj nowe wydarzenie'])

<body>
    @include('shared.navbar')
    <div class="container-fluid mt-5 px-5">
        <h1>Dodaj nowe wydarzenie</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('events.store', ['id' => $restaurant->id]) }}">
            @csrf

            <div class="mb-3">
                <label for="date" class="form-label">Data</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ old('date') }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="number_of_people" class="form-label">Liczba osób</label>
                <input type="number" name="number_of_people" id="number_of_people" class="form-control"
                    value="{{ old('number_of_people') }}" min="1" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Opis</label>
                <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="event_type_id" class="form-label">Typ wydarzenia</label>
                <select name="event_type_id" id="event_type_id" class="form-select" required>
                    <option value="">-- Wybierz typ --</option>
                    @foreach ($eventTypes as $type)
                        <option value="{{ $type->id }}" @selected(old('event_type_id') == $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @include('shared.menu-card')

            <div class="mt-3">
                <button type="submit" name="action" value="custom" class="btn btn-primary">
                    Utwórz własne menu
                </button>

                <button type="submit" name="action" value="event" class="btn btn-success">
                    Utwórz wydarzenie
                </button>
            </div>
        </form>
    </div>
</body>
