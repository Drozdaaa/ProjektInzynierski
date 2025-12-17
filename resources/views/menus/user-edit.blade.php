@include('shared.html')
@include('shared.head', ['pageTitle' => 'Edytuj menu wydarzenia'])
@include('shared.menu-scripts')

<body>
    @include('shared.navbar')
    <div class="container-fluid mt-5 px-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Edytuj menu dla wydarzenia</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
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

        @foreach ($menusToEdit as $menu)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edycja: {{ $menu->name }}</h4>
                </div>

                <div class="card-body">
                    <form class="menu-editor-form"
                        action="{{ route('menus.user.update', ['event' => $event->id, 'menu' => $menu->id]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="price_{{ $menu->id }}" class="form-label fw-bold">Cena menu (zł)</label>
                            <input type="number" step="0.01" min="0" name="price"
                                id="price_{{ $menu->id }}" class="form-control form-control-lg"
                                value="{{ old('price', $menu->price) }}" required>
                        </div>

                        <h5 class="mb-3 border-bottom pb-2">Wybierz dania do tego menu:</h5>

                        <div id="dishes-container-{{ $menu->id }}">
                            <table class="table table-borderless">
                                @foreach ($dishTypes as $dishType)
                                    <tr>
                                        <td>
                                            <h6 class="text-uppercase text-muted fw-bold mt-2">{{ $dishType->name }}
                                            </h6>
                                            <div class="row g-3">
                                                @forelse ($dishType->availableDishes as $dish)
                                                    @php
                                                        $selected = $menu->dishes->contains('id', $dish->id);
                                                    @endphp

                                                    @include('shared.dish-card', [
                                                        'dish' => $dish,
                                                        'selected' => $selected,
                                                        'menuId' => $menu->id,
                                                    ])
                                                @empty
                                                    <div class="col-12 text-muted fst-italic small">Brak dań w tej
                                                        kategorii.</div>
                                                @endforelse
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        <div class="mt-4 d-flex justify-content-left gap-2">
                            <a href="{{ $cancelUrl }}" class="btn btn-secondary">Anuluj</a>
                            <button type="submit" class="btn btn-success">
                                Zapisz zmiany
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

    </div>
</body>
