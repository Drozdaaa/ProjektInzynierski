@include('shared.html')
@include('shared.head', ['pageTitle' => 'Weryfikacja Email'])

<body>
    @include('shared.navbar')

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">Zweryfikuj swój adres email</div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <p>Przed rozpoczęciem, sprawdź swoją skrzynkę mailową, czy otrzymałeś link weryfikacyjny.</p>
                        <p>Jeśli nie otrzymałeś maila, kliknij przycisk poniżej, aby wysłać go ponownie.</p>

                        <form class="d-inline" method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Wyślij link ponownie</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
