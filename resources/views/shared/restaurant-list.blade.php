@forelse ($restaurants as $restaurant)
    <div class="card mb-4 shadow-sm">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="..." class="img-fluid rounded-start h-100"
                    style="object-fit: cover; min-height: 200px;" alt="{{ $restaurant->name }}">
            </div>
            <div class="col-md-8">
                <div class="card-body h-100 d-flex flex-column">
                    <div class="flex-grow-1">
                        <h5 class="card-title">{{ $restaurant->name }}</h5>
                        <p class="card-text text-muted">{{ $restaurant->description }}</p>
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $restaurant->address->city }},
                                ul. {{ $restaurant->address->street }}
                                {{ $restaurant->address->building_number }},
                                {{ $restaurant->address->postal_code }}
                            </small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <a href="{{ route('restaurants.show', ['id' => $restaurant->id]) }}"
                           class="btn btn-primary">
                            Rezerwuj teraz
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="alert alert-info text-center">
            <h4>Brak dostępnych lokali</h4>
            <p class="mb-0">Spróbuj zmienić kryteria wyszukiwania.</p>
        </div>
    </div>
@endforelse

@if ($restaurants->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $restaurants->links() }}
    </div>
@endif
