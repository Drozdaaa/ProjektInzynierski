@include('shared.html')
@include('shared.head', ['pageTitle' => 'Lista dań'])

<body>
    @include('shared.navbar')

    <div class="container-fluid mt-5 px-5">
        <h1 class="mb-4">Dania w restauracji</h1>

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
                                    @if ($dish->diets->isNotEmpty())
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($dish->diets as $diet)
                                                <li><span class="badge bg-success">{{ $diet->name }}</span></li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">Brak</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($dish->allergies->isNotEmpty())
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($dish->allergies as $allergy)
                                                <li><span class="badge bg-warning">{{ $allergy->name }}</span></li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">Brak</span>
                                    @endif
                                </td>

                                <td>{{ $dish->price }} zł</td>

                                <td>
                                    <a href="{{ route('dishes.edit', ['dish' => $dish->id]) }}" class="btn btn-sm btn-info">
                                        Edytuj
                                    </a>

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
</body>
