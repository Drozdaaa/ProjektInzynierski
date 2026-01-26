@include('shared.html')
@include('shared.head', ['pageTitle' => 'Edytuj menu wydarzenia'])
@include('shared.menu-scripts')

<body>
    @include('shared.navbar')
    <div class="container-fluid mt-5 px-5">

        <form class="menu-editor-form" action="{{ route('menus.user.update', ['event' => $event->id]) }}" method="POST"
            id="main-menu-form">

            @csrf
            @method('PUT')

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
                <div class="card mb-4 shadow-sm day-section">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Edycja: {{ $menu->name }}</h4>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Cena menu (zł)</label>
                            <input type="number" step="0.01" min="0" name="menus[{{ $menu->id }}][price]"
                                class="form-control form-control-lg day-price-input"
                                value="{{ old('menus.' . $menu->id . '.price', $menu->price) }}" required>
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
                                                        'uniqueSuffix' => '_menu_' . $menu->id,
                                                        'inputName' => "menus[{$menu->id}][dishes][]",
                                                    ])
                                                @empty
                                                    <div class="col-12 text-muted fst-italic small">
                                                        Brak dań w tej kategorii.
                                                    </div>
                                                @endforelse
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="fixed-bottom bg-white border-top p-3 px-5 shadow-md d-flex gap-2">
                <button type="submit" class="btn btn-success btn-md">
                    Zapisz zmiany
                </button>
                <a href="{{ $cancelUrl }}" class="btn btn-secondary btn-md">Anuluj</a>
            </div>

        </form>
    </div>
</body>
