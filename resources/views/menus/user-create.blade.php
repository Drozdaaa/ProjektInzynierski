@include('shared.html')
@include('shared.head', ['pageTitle' => 'Stwórz menu dla wydarzenia'])
@include('shared.menu-scripts')

<body>
    @include('shared.navbar')
    <div class="container-fluid mt-5 px-5">
        <h1>Stwórz własne menu dla wydarzenia</h1>

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('menus.user-store', ['restaurant' => $restaurant->id, 'event' => $event->id]) }}"
            method="POST" id="menu-form">
            @csrf

            <div class="mb-3">
                <h3>Cena menu</h3>
                <input type="number" step="0.01" min="0" name="price" id="price" class="form-control"
                    value="{{ old('price', 0) }}" required readonly>
            </div>

            <h3>Wybierz dania:</h3>

            <table class="table table-borderless" id="dishes-container">
                <tr>
                    <td>
                        <h4>Przystawki</h4>
                        <div class="row g-3">
                            @foreach ($dishes->where('dishType.name', 'Przystawka') as $dish)
                                @include('shared.dish-card', ['dish' => $dish])
                            @endforeach
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h4>Zupy</h4>
                        <div class="row g-3">
                            @foreach ($dishes->where('dishType.name', 'Zupa') as $dish)
                                @include('shared.dish-card', ['dish' => $dish])
                            @endforeach
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h4>Dania główne</h4>
                        <div class="row g-3">
                            @foreach ($dishes->where('dishType.name', 'Danie główne') as $dish)
                                @include('shared.dish-card', ['dish' => $dish])
                            @endforeach
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h4>Desery</h4>
                        <div class="row g-3">
                            @foreach ($dishes->where('dishType.name', 'Deser') as $dish)
                                @include('shared.dish-card', ['dish' => $dish])
                            @endforeach
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                Utwórz menu
                            </button>

                            <button type="submit" name="create_another" value="1" class="btn btn-primary">
                                Utwórz i dodaj kolejne menu
                            </button>

                            <a href="{{ route('events.create', ['id' => $restaurant->id]) }}" class="btn btn-secondary">
                                Anuluj
                            </a>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
