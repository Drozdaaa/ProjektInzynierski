@include('shared.html')
@include('shared.head', ['pageTitle' => 'Dodaj nowe wydarzenie'])

<body>
    @include('shared.navbar')
    <div class="container">
    <h2>Dodaj nowe wydarzenie</h2>

    <form method="POST" action="{{ route('events.store',['id'=>$restaurant->id]) }}">
        @csrf

        <div class="mb-3">
            <label for="date" class="form-label">Data</label>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="number_of_people" class="form-label">Liczba osób</label>
            <input type="number" name="number_of_people" id="number_of_people" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Opis</label>
            <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="event_type_id" class="form-label">Typ wydarzenia</label>
            <select name="event_type_id" id="event_type_id" class="form-select" required>
                @foreach ($eventTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="menu_id" class="form-label">Menu</label>
            <select name="menu_id" id="menu_id" class="form-select" required>
                @foreach ($menus as $menu)
                    <option value="{{ $menu->id }}">Menu {{ $menu->id }} (Cena: {{ $menu->price }} zł)</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Utwórz wydarzenie</button>
    </form>
</div>
</body>
