@include('shared.html')
@include('shared.head', ['pageTitle' => 'Dodaj nowe danie'])

<body>
    @include('shared.navbar')
    <div class="container-fluid mt-5 px-5">
        <h1>Dodaj nowe danie</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('dishes.store', ['restaurant' => $restaurant->id]) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nazwa dania</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Opis</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Cena (zł)</label>
                <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}"
                    step="0.01" min="0" required>
            </div>

            <div class="mb-3">
                <label for="dish_type_id" class="form-label">Typ dania</label>
                <select name="dish_type_id" id="dish_type_id" class="form-select" required>
                    @foreach ($dishTypes as $dishType)
                        <option value="{{ $dishType->id }}"
                            {{ old('dish_type_id') == $dishType->id ? 'selected' : '' }}>
                            {{ $dishType->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Rodzaje diet (Opcjonalne)</h6>
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                        @foreach ($diets as $diet)
                            <div class="col">
                                <input type="checkbox" class="btn-check" name="diets[]" value="{{ $diet->id }}"
                                    id="diet_{{ $diet->id }}" autocomplete="off"
                                    {{ in_array($diet->id, old('diets', [])) ? 'checked' : '' }}>
                                <label
                                    class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center py-3"
                                    for="diet_{{ $diet->id }}">
                                    <span class="fw-bold mb-1">{{ $diet->name }}</span>
                                    @if ($diet->description)
                                        <small style="font-size: 1.0rem; opacity: 0.8;">
                                            {{ $diet->description }}
                                        </small>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text">Alergeny (Zaznacz jeśli występują)</h6>
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                        @foreach ($allergies as $allergy)
                            <div class="col">
                                <input type="checkbox" class="btn-check" name="allergies[]" value="{{ $allergy->id }}"
                                    id="allergy_{{ $allergy->id }}" autocomplete="off"
                                    {{ in_array($allergy->id, old('allergies', [])) ? 'checked' : '' }}>

                                <label
                                    class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center py-3"
                                    for="allergy_{{ $allergy->id }}">

                                    <span class="fw-bold mb-1">{{ $allergy->name }}</span>

                                    @if ($allergy->description)
                                        <small style="font-size: 1.0rem; opacity: 0.8;">
                                            {{ $allergy->description }}
                                        </small>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Dodaj danie</button>
            <a href="{{ route('menus.index') }}" class="btn btn-secondary">Wróć do zarządzania menu</a>
        </form>
    </div>
</body>
