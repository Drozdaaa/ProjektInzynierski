@include('shared.html')
@include('shared.head', ['pageTitle' => 'Panel Administratora'])

<body>

    @include('shared.navbar')

    <h1>Panel Admina</h1>
    <div class="container">
        <div class="btn-group mt-3" role="group" aria-label="tabela przełączająca">
            <input type="radio" class="btn-check" name="btnradio" id="btn-users" autocomplete="off" checked
                onclick="showTable('users')">
            <label class="btn btn-outline-primary" for="btn-users">Użytkownicy</label>

            <input type="radio" class="btn-check" name="btnradio" id="btn-restaurants" autocomplete="off"
                onclick="showTable('restaurants')">
            <label class="btn btn-outline-primary" for="btn-restaurants">Lokale</label>
        </div>
        <div class="table-responsive-sm">
            <div id="table-users">
                <table class="table table-users table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Imię</th>
                            <th scope="col">Nazwisko</th>
                            <th scope="col">Email</th>
                            <th scope="col">Numer telefonu</th>
                            <th scope="col">Rola</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->role->name }}</td>
                                <td>
                                    <button type="button" class="btn btn-info">Edytuj</button>
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-danger">Usuń</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <th scope="row" colspan="5">Brak użytkowników.</th>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
            <div id="table-restaurants" class="d-none">
                <table class="table table-restaurants table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nazwa</th>
                            <th scope="col">Miejscowość</th>
                            <th scope="col">Ulica</th>
                            <th scope="col">Kod pocztowy</th>
                            <th scope="col">Numer budynku</th>
                            <th scope="col">Opis</th>
                            <th scope="col">Właściciel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($restaurants as $restaurant)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $restaurant->name }}</td>
                                <td>{{ $restaurant->address->city }}</td>
                                <td>{{ $restaurant->address->street }}</td>
                                <td>{{ $restaurant->address->postal_code }}</td>
                                <td>{{ $restaurant->address->building_number }}</td>
                                <td>{{ $restaurant->description }}</td>
                                <td>{{ $restaurant->user->first_name }} {{ $restaurant->user->last_name }}</td>
                                <td> <a href="{{ route('restaurants.edit', $restaurant->id) }}"
                                        class="btn btn-info">Edytuj</a></td>
                                <td>
                                    <form method="POST" action="{{ route('restaurants.destroy', $restaurant->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Usuń</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <th scope="row" colspan="10">Brak lokali do wyświetlenia.</th>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            function showTable(type) {
                const usersTable = document.getElementById('table-users');
                const restaurantsTable = document.getElementById('table-restaurants');

                if (type === 'users') {
                    usersTable.classList.remove('d-none');
                    restaurantsTable.classList.add('d-none');
                } else {
                    usersTable.classList.add('d-none');
                    restaurantsTable.classList.remove('d-none');
                }
            }
        </script>
</body>
