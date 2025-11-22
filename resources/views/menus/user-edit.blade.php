@include('shared.html')
@include('shared.head', ['pageTitle' => 'Edytuj menu wydarzenia'])
@include('shared.menu-scripts')

<body>
    @include('shared.navbar')
    <div class="container-fluid mt-5 px-5">
        <h1>Edytuj menu dla wydarzenia</h1>

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

        @foreach ($menus as $menu)
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <h4>Menu #{{ $loop->iteration }}</h4>
                </div>

                <div class="card-body">
                    <form class="menu-editor-form"
                        action="{{ route('menus.user.update', ['event' => $event->id, 'menu' => $menu->id]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="price_{{ $menu->id }}" class="form-label">Cena menu</label>
                            <input type="number" step="0.01" min="0" name="price"
                                id="price_{{ $menu->id }}" class="form-control"
                                value="{{ old('price', $menu->price) }}" required readonly>
                        </div>

                        <h5>Wybierz dania:</h5>
                        <table class="table table-borderless" id="dishes-container-{{ $menu->id }}">
                            @foreach (['Przystawka', 'Zupa', 'Danie główne', 'Deser'] as $type)
                                <tr>
                                    <td>
                                        <h6>{{ $type }}@if ($type == 'Danie główne')
                                                główne
                                            @endif
                                        </h6>
                                        <div class="row g-3">
                                            @foreach ($dishes->where('dishType.name', $type) as $dish)
                                                @php
                                                    $selected = $menu->dishes->pluck('id')->contains($dish->id);
                                                @endphp
                                                @include('shared.dish-card', [
                                                    'dish' => $dish,
                                                    'selected' => $selected,
                                                ])
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </table>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">Zapisz zmiany</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        <div class="mb-5">
            <a href="{{ route('users.user-dashboard') }}" class="btn btn-secondary">Powrót do wydarzenia</a>
        </div>
    </div>
</body>
