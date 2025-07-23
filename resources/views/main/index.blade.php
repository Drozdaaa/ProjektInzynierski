@include('shared.html')
@include('shared.head', ['pageTitle' => 'Main'])

<body>

    @include('shared.navbar')

    <div class="container mt-5">
        <div class="row">
            <h1>Lokale</h1>
        </div>
        <div class="row">
            @forelse ($restaurants as $restaurant)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $restaurant->name }}</h5>
                            <p class="card-text">{{ $restaurant->description }}</p>
                            <a href="{{route('restaurants.show', ['id' => $restaurant->id])}}" class="btn btn-primary">Więcej szczegółów...</a>
                        </div>
                    </div>
                </div>
            @empty
                <p>Brak wycieczek.</p>
            @endforelse
        </div>
    </div>

</body>
