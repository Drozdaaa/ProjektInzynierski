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
                                <h6 class="mt-3 border-bottom pb-1">{{ $type }}</h6>
                                <ul class="list-group mb-3">
                                    @foreach ($dishes as $dish)
                                        <li class="list-group-item text-start">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong>{{ $dish->name }}</strong>
                                                </div>
                                                <span class="text-nowrap fw-bold">{{ number_format($dish->price, 2) }}
                                                    zł</span>
                                            </div>

                                            <div class="mb-2 text-muted small">
                                                {{ $dish->description }}
                                            </div>
                                            <div class="mb-1" style="font-size: 0.85rem;">
                                                <span class="fw-bold">Diety:</span>
                                                @if ($dish->diets->isEmpty())
                                                    <span class="text-muted">Brak</span>
                                                @else
                                                    @foreach ($dish->diets as $diet)
                                                        <br>
                                                        <span class="d-inline-block me-1">
                                                            <span
                                                                class="text-success fw-semibold">{{ $diet->name }}</span>
                                                            @if ($diet->description)
                                                                <span
                                                                    class="text-muted fst-italic">({{ $diet->description }})</span>
                                                            @endif
                                                            {{ !$loop->last ? ',' : '' }}
                                                        </span>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div style="font-size: 0.85rem;">
                                                <span class="fw-bold">Alergeny:</span>
                                                @if ($dish->allergies->isEmpty())
                                                    <span class="text-muted">Brak</span>
                                                @else
                                                    @foreach ($dish->allergies as $allergy)
                                                        <br>
                                                        <span class="d-inline-block me-1">
                                                            <span
                                                                class="text-danger fw-semibold">{{ $allergy->name }}</span>
                                                            @if ($allergy->description)
                                                                <span
                                                                    class="text-muted fst-italic">({{ $allergy->description }})</span>
                                                            @endif
                                                            {{ !$loop->last ? ',' : '' }}
                                                        </span>
                                                    @endforeach
                                                @endif
                                            </div>
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
