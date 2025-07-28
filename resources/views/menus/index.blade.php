@include('shared.html')
@include('shared.head', ['pageTitle' => 'Menu'])

<body>
    @include('shared.navbar')
    <h1>Twoje aktualne menu</h1>
    <div class='container-fluid px-5'>
        <div class="btn-group" role="group" aria-label="Basic example">
            <a href="{{ route('dishes.create') }}" class="btn btn-primary">Dodaj danie</a>
            <a href="{{ route('menus.create') }}" class="btn btn-primary">Utwórz nowe menu</a>
        </div>
    </div>
</body>
