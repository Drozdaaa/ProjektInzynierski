@include('shared.html')
@include('shared.head', ['pageTitle' => $restaurant->name])

<body>
    @include('shared.navbar')
    <h1>{{$restaurant->name}}</h1>
    <div class="container-fluid px-5">
        <a href="{{route ('events.create')}}" class="btn btn-primary">Dodaj wydarzenie</a>
    </div>
</body>
