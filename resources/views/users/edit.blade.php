@include('shared.html')
@include('shared.head', ['pageTitle' => 'Moje konto'])

<body>
    @include('shared.navbar')

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        Wystąpiły błędy w formularzu. Sprawdź poprawność danych.
                    </div>
                @endif
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Dane osobowe</h4>
                        <span>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#editProfileModal">
                                    <i class="bi bi-pencil-square"></i> Edytuj dane
                                </button>
                            </div>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Imię</div>
                            <div class="col-sm-8 fw-bold">{{ $user->first_name }}</div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Nazwisko</div>
                            <div class="col-sm-8 fw-bold">{{ $user->last_name }}</div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Adres e-mail</div>
                            <div class="col-sm-8">{{ $user->email }}</div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Numer telefonu</div>
                            <div class="col-sm-8">{{ $user->phone ?? '-' }}</div>
                        </div>
                        <hr>
                    </div>
                </div>
                <div class="card shadow-sm border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Bezpieczeństwo i ustawienia konta</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong>Zmiana hasła</strong>
                            </div>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#changePasswordModal">
                                Zmień hasło
                            </button>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-danger">Usuwanie konta</strong>
                                <p class="text-muted small mb-0">Ta operacja jest nieodwracalna. Wszystkie Twoje dane
                                    zostaną usunięte.</p>
                            </div>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                Usuń konto
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edycja danych</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">Imię</label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Nazwisko</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Adres e-mail</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Numer telefonu</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('users.update-password', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Zmiana hasła</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Obecne hasło</label>
                            <input type="password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nowe hasło</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Potwierdź nowe hasło</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-primary">Zmień hasło</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Usuń konto</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body text-center p-4">
                        <i class="bi bi-exclamation-triangle-fill text-danger display-4 mb-3"></i>
                        <h4 class="mb-3">Czy na pewno?</h4>
                        <p class="mb-0">Ta operacja jest <strong>nieodwracalna</strong>. Twoje konto oraz wszystkie
                            powiązane z nim dane zostaną trwale usunięte.</p>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-danger">Tak, usuń konto trwale</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if ($errors->has('current_password') || $errors->has('password'))
                    new bootstrap.Modal(document.getElementById('changePasswordModal')).show();
                @elseif ($errors->has('first_name') || $errors->has('last_name') || $errors->has('email') || $errors->has('phone'))
                    new bootstrap.Modal(document.getElementById('editProfileModal')).show();
                @endif
            });
        </script>
    @endif
</body>

</html>
