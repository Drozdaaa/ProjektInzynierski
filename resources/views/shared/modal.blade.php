@php
    $menusForModal = isset($menu) ? collect([$menu]) : (isset($event) ? $event->menus : collect());
@endphp

@if ($menusForModal->isNotEmpty())
    <div class="modal fade" id="menuDetailsModal{{ isset($menu) ? $menu->id : $event->id }}" tabindex="-1"
        aria-labelledby="menuDetailsLabel{{ isset($menu) ? $menu->id : $event->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content text-start">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuDetailsLabel{{ isset($menu) ? $menu->id : $event->id }}">
                        Szczegóły menu{{ isset($event) ? ' przypisanych do wydarzenia' : '' }}:
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
                </div>

                <div class="modal-body">
                    @foreach ($menusForModal as $menuItem)
                        <div class="border rounded p-3 mb-4 shadow-sm">
                            <h5 class="mb-1">
                                {{ $menuItem->name }}
                                <span class="badge bg-primary">{{ number_format($menuItem->price, 2) }} zł</span>
                                @if (isset($menuItem->pivot) && $menuItem->pivot->amount)
                                    <span class="ms-3">Ilość porcji:
                                        {{ $menuItem->pivot->amount }}</span>
                                @endif
                            </h5>

                            @foreach ($menuItem->dishesByType ?? [] as $type => $dishes)
                                <h6 class="mt-3">{{ $type }}</h6>
                                <ul class="list-group mb-3">
                                    @foreach ($dishes as $dish)
                                        <li class="list-group-item text-start">
                                            <strong>{{ $dish->name }}</strong> - {{ number_format($dish->price, 2) }}
                                            zł<br>
                                            {{ $dish->description }}<br>
                                            <small>Diety:
                                                {{ $dish->diets->pluck('name')->join(', ') ?: 'Brak' }}</small><br>
                                            <small>Alergeny:
                                                {{ $dish->allergies->pluck('name')->join(', ') ?: 'Brak' }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
