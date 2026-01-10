@include('shared.html')
@include('shared.head', ['pageTitle' => 'Lista dań'])

<body>
    @include('shared.navbar')

    <div class="container-fluid mt-5 px-5">
        <h1 class="mb-4">Dania w restauracji</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ route('menus.index') }}" class="btn btn-secondary me-2">Wróć do zarządzania menu</a>
            <a href="{{ route('dishes.create', ['restaurant' => $restaurant->id]) }}" class="btn btn-primary">
                Dodaj danie
            </a>
        </div>

        @if ($dishes->isEmpty())
            <div class="alert alert-info">Brak dań w tej restauracji.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped align-middle text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>Nazwa</th>
                            <th>Opis</th>
                            <th>Rodzaj</th>
                            <th>Diety</th>
                            <th>Alergie</th>
                            <th>Cena</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dishes as $dish)
                            <tr>
                                <td class="fw-semibold">{{ $dish->name }}</td>
                                <td>{{ $dish->description }}</td>
                                <td>{{ $dish->dishType->name ?? 'Brak' }}</td>
                                <td>
                                    @foreach ($dish->diets as $diet)
                                        <span class="badge bg-success">{{ $diet->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($dish->allergies as $allergy)
                                        <span class="badge bg-warning">{{ $allergy->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $dish->price }} zł</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info edit-dish-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editDishModal"
                                        data-id="{{ $dish->id }}"
                                        data-name="{{ $dish->name }}"
                                        data-price="{{ $dish->price }}"
                                        data-description="{{ $dish->description }}"
                                        data-type="{{ $dish->dish_type_id }}"
                                        data-diets="{{ json_encode($dish->diets->pluck('id')) }}"
                                        data-allergies="{{ json_encode($dish->allergies->pluck('id')) }}">
                                        Edytuj
                                    </button>

                                    <form action="{{ route('dishes.destroy', $dish->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Czy na pewno chcesz usunąć to danie?')">
                                            Usuń
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="modal fade" id="editDishModal" tabindex="-1" aria-labelledby="editDishModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editDishForm" method="POST" action="">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title" id="editDishModalLabel">Edytuj danie</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nazwa dania</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Cena (zł)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="edit_price" name="price" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Opis</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="2" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_dish_type" class="form-label">Typ dania</label>
                            <select class="form-select" id="edit_dish_type" name="dish_type_id" required>
                                @foreach($dishTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Diety</label>
                                <div class="card p-2" style="max-height: 150px; overflow-y: auto;">
                                    @foreach($diets as $diet)
                                        <div class="form-check">
                                            <input class="form-check-input edit-diet-checkbox" type="checkbox"
                                                   name="diets[]" value="{{ $diet->id }}" id="diet_edit_{{ $diet->id }}">
                                            <label class="form-check-label" for="diet_edit_{{ $diet->id }}">
                                                {{ $diet->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Alergie</label>
                                <div class="card p-2" style="max-height: 150px; overflow-y: auto;">
                                    @foreach($allergies as $allergy)
                                        <div class="form-check">
                                            <input class="form-check-input edit-allergy-checkbox" type="checkbox"
                                                   name="allergies[]" value="{{ $allergy->id }}" id="allergy_edit_{{ $allergy->id }}">
                                            <label class="form-check-label" for="allergy_edit_{{ $allergy->id }}">
                                                {{ $allergy->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/dish_modal.js') }}"></script>
</body>
