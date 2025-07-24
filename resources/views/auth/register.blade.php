@include('shared.html')

@include('shared.head', ['pageTitle' => 'Zarejestruj się'])

<body>
    <div class="container mt-5 mb-5">
        <div class="row mt-4 mb-4 text-center">
            <h1>Zarejestruj się</h1>
        </div>

        <div class="row d-flex justify-content-center">
            <div class="col-10 col-sm-10 col-md-6 col-lg-4">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
                    @csrf

                    <div class="form-group mb-2">
                        <label for="first_name" class="form-label">Imię</label>
                        <input id="first_name" name="first_name" type="text"
                            class="form-control @error('first_name') is-invalid @enderror"
                            value="{{ old('first_name') }}" required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="last_name" class="form-label">Nazwisko</label>
                        <input id="last_name" name="last_name" type="text"
                            class="form-control @error('last_name') is-invalid @enderror"
                            value="{{ old('last_name') }}" required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="phone" class="form-label">Telefon</label>
                        <input id="phone" name="phone" type="text"
                            class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone') }}" required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="password" class="form-label">Hasło</label>
                        <input id="password" name="password" type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="role_id" class="form-label">Typ konta</label>
                        <select id="role_id" name="role_id"
                            class="form-control @error('role_id') is-invalid @enderror" required>
                            <option value="">-- Wybierz --</option>
                            <option value="2" {{ old('role_id') == 2 ? 'selected' : '' }}>Klient</option>
                            <option value="3" {{ old('role_id') == 3 ? 'selected' : '' }}>Menadżer</option>
                        </select>
                    </div>

                    <div class="text-center mb-4">
                        <input class="btn btn-success" type="submit" value="Zarejestruj się">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
