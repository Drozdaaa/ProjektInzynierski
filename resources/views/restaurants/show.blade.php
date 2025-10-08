@include('shared.html')
@include('shared.head', ['pageTitle' => $restaurant->name])

<body>
    @include('shared.navbar')
    <div class="container-fluid mt-5 px-5">
    <h1>{{ $restaurant->name }}</h1>
        <a href="{{ route('events.create', ['id' => $restaurant->id]) }}" class="btn btn-primary">Dodaj wydarzenie</a>
    </div>
</body>
