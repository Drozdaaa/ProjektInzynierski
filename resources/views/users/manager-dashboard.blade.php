@include('shared.html')
@include('shared.head', ['pageTitle' => 'Panel Menadżera'])

<body>
    @include('shared.navbar')

    <h1>Panel Menadżera</h1>
    <div class = 'container-fluid px-5'>
        <h3>Nadchodzące wydarzenia</h3>
        <div class="table-responsive-sm">
            <div id="table-events">
                <table class="table table-events table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Imię i nazwisko</th>
                            <th scope="col">Numer telefonu</th>
                            <th scope="col">Email</th>
                            <th scope="col">Rodzaj wydarzenia</th>
                            <th scope="col">Data</th>
                            <th scope="col">Liczba osób</th>
                            <th scope="col">Opis</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $event)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $event->user->first_name }} {{ $event->user->last_name }}</td>
                                <td>{{ $event->user->phone }}</td>
                                <td>{{ $event->user->email }}</td>
                                <td>{{ $event->eventType->name }}</td>
                                <td>{{ $event->date }}</td>
                                <td>{{ $event->number_of_people }}</td>
                                <td>{{ $event->description }}</td>
                                <td>{{ $event->status->name }}</td>
                                <td>
                                       <a href="{{ route('menu.show', $event->id) }}" class="btn btn-primary">Zobacz menu</a>
                                    <button type="button" class="btn btn-info">Edytuj</button>
                                    <button type="submit" class="btn btn-danger">Usuń</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <th scope="row" colspan="5">Brak wydarzeń.</th>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>



</body>
