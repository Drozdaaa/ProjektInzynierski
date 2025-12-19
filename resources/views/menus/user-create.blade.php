@include('shared.html')
@include('shared.head', ['pageTitle' => 'Stwórz menu dla wydarzenia'])
@include('shared.menu-scripts')

<body>
    @include('shared.navbar')
    <div class="container-fluid mt-5 px-5">
        <h1>Stwórz własne menu dla wydarzenia</h1>

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('menus.user-store', ['restaurant' => $restaurant->id]) }}"
            method="POST" id="menu-form">
            @csrf

            @foreach($events as $dayEvent)

                <div class="day-section border-bottom mb-5 pb-4" data-event-id="{{ $dayEvent->id }}">

                    <h2 class="text-primary mt-4">
                        Dzień {{ $loop->iteration }} <small class="text-muted h5">({{ $dayEvent->date }})</small>
                    </h2>

                    <div class="mb-3">
                        <label class="fw-bold">Szacowana cena menu (Dzień {{ $loop->iteration }}):</label>
                        <input type="number" step="0.01" min="0"
                               name="price_display_{{ $dayEvent->id }}"
                               class="form-control day-price-input"
                               value="0.00" style="max-width: 200px;" required readonly>
                    </div>

                    <h3>Wybierz dania:</h3>

                    <table class="table table-borderless dishes-container">
                        <tr>
                            <td>
                                <h4>Przystawki</h4>
                                <div class="row g-3">
                                    @foreach ($dishes->where('dishType.name', 'Przystawka') as $dish)
                                        @include('shared.dish-card', [
                                            'dish' => $dish,
                                            'inputName' => "menus[{$dayEvent->id}][dishes][]",
                                            'uniqueSuffix' => "_{$dayEvent->id}"
                                        ])
                                    @endforeach
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <h4>Zupy</h4>
                                <div class="row g-3">
                                    @foreach ($dishes->where('dishType.name', 'Zupa') as $dish)
                                        @include('shared.dish-card', [
                                            'dish' => $dish,
                                            'inputName' => "menus[{$dayEvent->id}][dishes][]",
                                            'uniqueSuffix' => "_{$dayEvent->id}"
                                        ])
                                    @endforeach
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <h4>Dania główne</h4>
                                <div class="row g-3">
                                    @foreach ($dishes->where('dishType.name', 'Danie główne') as $dish)
                                        @include('shared.dish-card', [
                                            'dish' => $dish,
                                            'inputName' => "menus[{$dayEvent->id}][dishes][]",
                                            'uniqueSuffix' => "_{$dayEvent->id}"
                                        ])
                                    @endforeach
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <h4>Desery</h4>
                                <div class="row g-3">
                                    @foreach ($dishes->where('dishType.name', 'Deser') as $dish)
                                        @include('shared.dish-card', [
                                            'dish' => $dish,
                                            'inputName' => "menus[{$dayEvent->id}][dishes][]",
                                            'uniqueSuffix' => "_{$dayEvent->id}"
                                        ])
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach

            <div class="mt-4 mb-5 pb-5 d-flex gap-2 justify-content-center">
                <button type="submit" class="btn btn-success btn-md">
                    Zapisz menu
                </button>

                <button type="submit" name="create_another" value="1" class="btn btn-primary btn-md">
                    Utwórz i dodaj kolejne menu
                </button>

                <a href="{{ route('events.show', ['restaurant' => $restaurant->id, 'event' => $event->id]) }}" class="btn btn-secondary btn-md">
                    Anuluj
                </a>
            </div>

        </form>
    </div>
</body>
