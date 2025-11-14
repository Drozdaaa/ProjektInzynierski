<div class="modal fade" id="menuDetailsModal{{ $menu->id }}" tabindex="-1"
    aria-labelledby="menuDetailsLabel{{ $menu->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content text-start">
            <div class="modal-header">
                <h5 class="modal-title" id="menuDetailsLabel{{ $menu->id }}">
                    Szczegóły menu:
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
            </div>
            <div class="modal-body text-start">
                <div class="border rounded p-3 mb-4 shadow-sm">
                    <p><strong>Cena menu:</strong> {{ number_format($menu->price, 2) }} zł</p>
                    @foreach ($menu->dishesByType ?? [] as $type => $dishes)
                        <h6 class="mt-3">{{ $type }}</h6>
                        <ul class="list-group mb-3">
                            @foreach ($dishes as $dish)
                                <li class="list-group-item text-start">
                                    <strong>{{ $dish->name }}</strong> {{ $dish->price }} zł<br>
                                    {{ $dish->description }}<br>
                                    <small>Diety: {{ $dish->diets->pluck('name')->join(', ') ?: 'Brak' }}</small><br>
                                    <small>Alergeny: {{ $dish->allergies->pluck('name')->join(', ') ?: 'Brak' }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
