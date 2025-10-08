@include('shared.html')
@include('shared.head', ['pageTitle' => 'Edytuj menu'])
@include('shared.menu-scripts')

<body>
    @include('shared.navbar')
    <div class="container-fluid mt-5 px-5">
        <h1>Edytuj menu: {{ $menu->id }}</h1>

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

        <form action="{{ route('menus.update', $menu->id) }}" method="POST" id="menu-form">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <h3>Cena menu</h3>
                <input type="number" step="0.01" min="0" name="price" id="price" class="form-control"
                    value="{{ old('price', $menu->price) }}" required readonly>
            </div>

            <h3>Wybierz dania:</h3>

            <table class="table table-borderless" id="dishes-container">
                @foreach (['Przystawka', 'Zupa', 'Danie główne', 'Deser'] as $type)
                    <tr>
                        <td>
                            <h4>{{ $type }}@if ($type == 'Danie główne')
                                    główne
                                @endif
                            </h4>
                            <div class="row g-3">
                                @foreach ($dishes->where('dishType.name', $type) as $dish)
                                    @include('shared.dish-card', [
                                        'dish' => $dish,
                                        'selected' => in_array($dish->id, $menu->dishes->pluck('id')->toArray()),
                                    ])
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <td>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">Zapisz zmiany</button>
                            <a href="{{ route('menus.index') }}" class="btn btn-secondary">Anuluj</a>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
