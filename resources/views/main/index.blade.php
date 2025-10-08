@include('shared.html')
@include('shared.head', ['pageTitle' => 'Strona główna'])

<body>
    @include('shared.navbar')

    <div class="container-fluid mt-5 px-5">
        <div class="row">
            <div class="col-12 mb-4">
                <h1>Lokale</h1>
            </div>
        </div>

        <div class="row">
            <form id="filters-form" action="{{ route('main.index') }}" method="GET">
                <div class="row">
                    <div class="col-lg-3 col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Filtry</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="city" class="form-label">Miasto</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                           value="{{ request('city') }}" placeholder="Wpisz miasto">
                                </div>
                                <div class="mb-3">
                                    <label for="street" class="form-label">Ulica</label>
                                    <input type="text" class="form-control" id="street" name="street"
                                           value="{{ request('street') }}" placeholder="Wpisz ulicę">
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Zastosuj filtry</button>
                                    <a href="{{ route('main.index') }}" class="btn btn-outline-secondary">Wyczyść filtry</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-9 col-md-8" id="restaurant-list">
                        @include('shared.restaurant-list', ['restaurants' => $restaurants])
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/main.js') }}"></script>
</body>
