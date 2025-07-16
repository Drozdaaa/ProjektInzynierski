@include('shared.html')
@include('shared.head', ['pageTitle' => 'Menu'])

<body>
    @include('shared.navbar')

    <div id="menu" class="container mt-5 mb-5">
        <div class="row m-2 text-center">
            <h1>Menu</h1>
        </div>

        <div class="row d-flex justify-content-center">
            <div class="col-12 col-sm-10 col-lg-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Dania w menu</h3>
                    </div>

                    <div class="card-body">
                        @forelse ($menu->dishes as $dish)
                            <div class="mb-3 border-bottom pb-2">
                                <h5>{{ $dish->name }} - {{ $dish->price }} zł</h5>
                                <p class="mb-1 text-muted">{{ $dish->dishType->name }}</p>
                            </div>
                        @empty
                            <p class="text-muted">Brak dań w tym menu.</p>
                        @endforelse
                    </div>

                    <div class="card-footer text-center">
                        <strong>Cena za menu: {{ $menu->price }} zł za talerz</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>



</body>
