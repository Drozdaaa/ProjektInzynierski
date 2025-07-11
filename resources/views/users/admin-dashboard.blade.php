@include('shared.html')
@include('shared.head', ['pageTitle' => 'Panel Administratora'])

<body>

    @include('shared.navbar')

    <h1>Panel Admina</h1>
    <div class="container">
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="btnradio" id="użykownicy" autocomplete="off" checked>
            <label class="btn btn-outline-primary" for="użykownicy">użykownicy</label>

            <input type="radio" class="btn-check" name="btnradio" id="lokale" autocomplete="off">
            <label class="btn btn-outline-primary" for="lokale">lokale</label>

        </div>
        <div class="table-responsive-sm">
            <table class="table table-hover table-striped">
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

                        </tr>
                    @empty
                        <tr>
                            <th scope="row" colspan="5">Brak użytkowników.</th>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>
</body>
