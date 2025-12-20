<div id="table-restaurants" class="d-none">
    <div class="card mb-4 bg-light">
        <div class="card-body">
            <form action="{{ route('users.admin-dashboard') }}" method="GET" class="row g-3 align-items-end">

                <div class="col-md-5">
                    <label class="form-label fw-bold">Nazwa lokalu</label>
                    <input type="text" name="restaurant_search" class="form-control"
                        value="{{ request('restaurant_search') }}" placeholder="Wpisz nazwę...">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Miasto</label>
                    <select name="restaurant_city" class="form-select">
                        <option value="">Wszystkie</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city }}" @selected(request('restaurant_city') == $city)>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"
                        onclick="localStorage.setItem('adminTab', 'restaurants')">
                        Filtruj
                    </button>
                    <a href="{{ route('users.admin-dashboard') }}" class="btn btn-outline-secondary"
                        onclick="localStorage.setItem('adminTab', 'restaurants')">
                        Wyczyść
                    </a>
                </div>
            </form>
        </div>
    </div>

    <table class="table table-striped align-middle text-center">
        <thead class="table-primary">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Nazwa</th>
                <th scope="col">Miejscowość</th>
                <th scope="col">Ulica</th>
                <th scope="col">Kod pocztowy</th>
                <th scope="col">Właściciel</th>
                <th scope="col">Akcje</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($restaurants as $restaurant)
                <tr>
                    <th scope="row">{{ $restaurants->firstItem() + $loop->index }}</th>
                    <td>{{ $restaurant->name }}</td>
                    <td>{{ $restaurant->address->city }}</td>
                    <td>{{ $restaurant->address->street }} {{ $restaurant->address->building_number }}</td>
                    <td>{{ $restaurant->address->postal_code }}</td>
                    <td>{{ $restaurant->user->first_name }} {{ $restaurant->user->last_name }}</td>
                    <td class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-info btn-sm"
                            onclick="openRestaurantModal({{ $restaurant->id }}, '{{ $restaurant->name }}', '{{ $restaurant->description }}', '{{ $restaurant->address->street }}', '{{ $restaurant->address->building_number }}', '{{ $restaurant->address->city }}', '{{ $restaurant->address->postal_code }}', {{ $restaurant->user_id }})">
                            Edytuj
                        </button>

                        <form method="POST" action="{{ route('restaurants.destroy', $restaurant->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Na pewno usunąć ten lokal?')">
                                Usuń</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <th scope="row" colspan="7" class="text-center">Brak lokali spełniających kryteria.</th>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $restaurants->links('pagination::bootstrap-5') }}
    </div>
</div>
