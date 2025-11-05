<div class="modal fade" id="menuDetailsModal{{ $event->id ?? $menu->id }}" tabindex="-1"
    aria-labelledby="menuDetailsLabel{{ $event->id ?? $menu->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="menuDetailsLabel{{ $event->id ?? $menu->id }}">
                    @if (isset($event))
                        Szczegóły menu dla wydarzenia: {{ $event->eventType->name }}
                    @else
                        Szczegóły menu:
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
            </div>

            <div class="modal-body">
                @if (isset($event))
                    @if ($event->menus->isEmpty())
                        <p class="text-muted"><em>Brak przypisanych menu.</em></p>
                    @else
                        @foreach ($event->menus as $menu)
                            <div class="border rounded p-3 mb-4 shadow-sm">
                                <h5 class="text-primary mb-3">
                                    Menu #{{ $loop->iteration }}
                                </h5>
                                <p><strong>Cena:</strong> {{ number_format($menu->price, 2) }} zł</p>

                                @if ($menu->dishes->isEmpty())
                                    <p class="text-muted"><em>Brak przypisanych dań.</em></p>
                                @else
                                    @foreach ($menu->dishesByType ?? [] as $type => $dishes)
                                        <h6 class="mt-3">{{ $type }}</h6>
                                        <ul class="list-group mb-3">
                                            @foreach ($dishes as $dish)
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between">
                                                        <strong>{{ $dish->name }}</strong>
                                                        <span>{{ $dish->price }} zł</span>
                                                    </div>
                                                    <small>Diety:
                                                        {{ $dish->diets->pluck('name')->join(', ') ?: 'Brak' }}</small><br>
                                                    <small>Alergeny:
                                                        {{ $dish->allergies->pluck('name')->join(', ') ?: 'Brak' }}</small>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    @endif
                @else
                    <div class="border rounded p-3 mb-4 shadow-sm">
                        <p><strong>Cena:</strong> {{ number_format($menu->price, 2) }} zł</p>

                        @if ($menu->dishes->isEmpty())
                            <p class="text-muted"><em>Brak przypisanych dań.</em></p>
                        @else
                            @foreach ($menu->dishesByType ?? [] as $type => $dishes)
                                <h6 class="mt-3">{{ $type }}</h6>
                                <ul class="list-group mb-3">
                                    @foreach ($dishes as $dish)
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <strong>{{ $dish->name }}</strong>
                                                <span>{{ $dish->price }} zł</span>
                                            </div>
                                            <small>Diety:
                                                {{ $dish->diets->pluck('name')->join(', ') ?: 'Brak' }}</small><br>
                                            <small>Alergeny:
                                                {{ $dish->allergies->pluck('name')->join(', ') ?: 'Brak' }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
