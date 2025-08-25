@include('shared.html')
@include('shared.head', ['pageTitle' => 'Stwórz nowe menu'])
@include('shared.menu-scripts')

<body>
    @include('shared.navbar')
    <div class="container mt-5">
        <h1>Stwórz nowe menu</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('menus.store', ['restaurant' => $restaurant->id]) }}" method="POST" id="menu-form">
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
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">Utwórz menu</button>
                            <a href="{{ route('menus.index') }}" class="btn btn-secondary">Zakończ</a>
                        </div>

                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
