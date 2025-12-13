<div class="mb-3">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach ($restaurant->menus as $menu)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $menu->name }}</h5>

                        @if ($menu->dishes->isEmpty())
                            <p class="text-muted"><em>Brak przypisanych dań.</em></p>
                        @else
                            <ul class="list-group list-group-flush mb-3">
                                @foreach ($menu->dishesByType as $type => $dishes)
                                    <li class="list-group-item">
                                        <strong>{{ $type }}:</strong>
                                        {{ $dishes->pluck('name')->join(', ') }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">
                            Cena menu: {{ $menu->price }} zł
                        </span>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#menuDetailsModal{{ $menu->id }}">
                                Szczegóły
                            </button>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="menus_id[]"
                                    id="menu_{{ $menu->id }}" value="{{ $menu->id }}"
                                    @checked(in_array($menu->id, old('menus_id', [])))>
                                <label class="form-check-label" for="menu_{{ $menu->id }}">
                                    Wybierz
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('shared.modal', ['menu' => $menu])
        @endforeach
    </div>
</div>
