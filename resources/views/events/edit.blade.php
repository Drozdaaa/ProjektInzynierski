@include('shared.html')
@include('shared.head', ['pageTitle' => 'Edytuj dane wydarzenia'])

<body>
    @include('shared.navbar')
    <div class="container mt-5">
        <h2>Edytuj dane wydarzenia</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('events.update', $event->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="date" class="form-label">Data wydarzenia</label>
                <input type="date" class="form-control" id="date" name="date"
                    value="{{ old('date', $event->date) }}" required>
            </div>

            <div class="mb-3">
                <label for="number_of_people" class="form-label">Liczba osób</label>
                <input type="number" class="form-control" id="number_of_people" name="number_of_people"
                    value="{{ old('number_of_people', $event->number_of_people) }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Opis</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $event->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="event_type_id" class="form-label">Typ wydarzenia</label>
                <select class="form-select" id="event_type_id" name="event_type_id" required>
                    @foreach ($eventTypes as $type)
                        <option value="{{ $type->id }}" {{ $event->event_type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="menu_id" class="form-label">Menu</label>
                <select class="form-select" id="menu_id" name="menu_id" required>
                    @foreach ($menus as $menu)
                        <option value="{{ $menu->id }}" {{ $event->menu_id == $menu->id ? 'selected' : '' }}>
                            Menu {{ $menu->id }} (Cena: {{ $menu->price }} zł)
                        </option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
            <a href="{{ route('users.manager-dashboard') }}" class="btn btn-secondary">Anuluj</a>
        </form>
    </div>
</body>
