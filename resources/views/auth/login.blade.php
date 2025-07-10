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

                    <div class="text-center mt-4 mb-4">
                        <input class="btn btn-primary" type="submit" value="Wyślij">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
