@include('shared.html')

@include('shared.head', ['pageTitle' => 'Zaloguj się'])

<body>
    <div class="container mt-5 mb-5">
        <div class="row mt-4 mb-4 text-center">
            <h1>Zaloguj się</h1>
        </div>

        <div class="row d-flex justify-content-center">
            <div class="col-10 col-sm-10 col-md-6 col-lg-4">

                @if ($errors->has('login'))
                    <div class="alert alert-danger text-center">
                        {{ $errors->first('login') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.authenticate') }}" class="needs-validation" novalidate>
                    @csrf

                    @if (request()->has('redirect_to'))
                        <input type="hidden" name="redirect_to" value="{{ request()->get('redirect_to') }}">
                    @endif

                    <input type="hidden" name="previous_url" value="{{ old('previous_url', $previousUrl ?? route('main.index')) }}">

                    <div class="form-group mb-2">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email"
                            class="form-control @if ($errors->has('email')) is-invalid @endif"
                            value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="password" class="form-label">Hasło</label>
                        <input id="password" name="password" type="password"
                            class="form-control @if ($errors->has('password')) is-invalid @endif" required>
                    </div>

                    <div class="mb-2 text-end">
                        <a href="{{ route('password.request') }}" class="text-decoration-none small">
                            Zapomniałeś hasła?
                        </a>
                    </div>

                    <div class="text-center mt-4 mb-4 d-grid gap-2 col-6 mx-auto">
                        <input class="btn btn-primary" type="submit" value="Zaloguj się">
                        <a href="{{ old('previous_url', $previousUrl ?? route('main.index')) }}" class="btn btn-secondary">
                            Wróć
                        </a>
                    </div>

                    <div class="text-center mt-4 mb-4">
                        <a href="{{ route('register.form') }}" class="btn btn-link">Zarejestruj się</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
