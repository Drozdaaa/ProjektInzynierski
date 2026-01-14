@include('shared.html')
@include('shared.head', ['pageTitle' => 'Tworzenie sal'])

<body>
    @include('shared.navbar')
    <div class="container-fluid mt-5 px-5">
        <h1>Sale w restauracji: {{ $restaurant->name }}</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if ($restaurant->rooms->count())
            <ul class="list-group mb-4">
                @foreach ($restaurant->rooms as $room)
                    <li class="list-group-item">
                        <strong>{{ $room->name }}</strong> - {{ $room->capacity }} osób
                    </li>
                @endforeach
            </ul>
        @endif
        <hr>
        <h3>Dodaj kolejną salę</h3>
        <form action="{{ route('rooms.store', $restaurant->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Nazwa sali</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            </div>

            <div class="mb-3">
                <label>Pojemność</label>
                <input type="number" name="capacity" class="form-control" value="{{ old('capacity') }}" min="1">
            </div>

            <div class="mb-3">
                <label>Cena za wynajem (zł)</label>
                <input type="number" name="price" class="form-control" value="{{ old('price') }}" step="0.01"
                    min="0">
            </div>

            <div class="mb-3">
                <label class="form-label">Czas potrzebny na sprzątanie (po wydarzeniu)</label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="number" name="cleaning_hours" class="form-control"
                                value="{{ old('cleaning_hours', 0) }}" min="0" placeholder="0">
                            <span class="input-group-text">Godz.</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="number" name="cleaning_minutes" class="form-control"
                                value="{{ old('cleaning_minutes', 0) }}" min="0" max="59" placeholder="0">
                            <span class="input-group-text">Min.</span>
                        </div>
                    </div>
                </div>
                <div class="form-text">Ten czas zostanie automatycznie doliczony do rezerwacji jako bufor techniczny.
                </div>
            </div>

            <div class="mb-3">
                <label>Opis (opcjonalnie)</label>
                <input type="text" name="description" class="form-control" value="{{ old('description') }}">
            </div>

            <button type="submit" name="action" value="add" class="btn btn-primary">Dodaj salę</button>
            <a href="{{ route('users.manager-dashboard') }}" class="btn btn-success">
                Zakończ dodawanie
            </a>
        </form>

    </div>
</body>
