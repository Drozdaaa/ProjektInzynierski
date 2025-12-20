@include('shared.html')
@include('shared.head', ['pageTitle' => 'Panel Administratora'])

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<body>
    @include('shared.navbar')

    <div class="container-fluid mt-5 px-5">
        <h1>Panel Admina</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="btn-group mt-3 mb-4" role="group" aria-label="tabela przełączająca">
            <input type="radio" class="btn-check" name="btnradio" id="btn-users" autocomplete="off" checked
                onclick="showTable('users')">
            <label class="btn btn-outline-primary" for="btn-users">Użytkownicy</label>

            <input type="radio" class="btn-check" name="btnradio" id="btn-restaurants" autocomplete="off"
                onclick="showTable('restaurants')">
            <label class="btn btn-outline-primary" for="btn-restaurants">Lokale</label>

            <input type="radio" class="btn-check" name="btnradio" id="btn-events" autocomplete="off"
                onclick="showTable('events')">
            <label class="btn btn-outline-primary" for="btn-events">Wydarzenia</label>
        </div>

        <div class="table-responsive-sm">
            @include('users.admin-users')
            @include('users.admin-restaurants')
            @include('users.admin-events')
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="userForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_type" value="admin_user_edit">

                    <div class="modal-header">
                        <h5 class="modal-title">Edytuj użytkownika</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Imię</label>
                            <input type="text" name="first_name" id="user_first_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nazwisko</label>
                            <input type="text" name="last_name" id="user_last_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="user_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefon</label>
                            <input type="text" name="phone" id="user_phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rola</label>
                            <select name="role_id" id="user_role_id" class="form-select">
                                <option value="1">Administrator</option>
                                <option value="2">Klient</option>
                                <option value="3">Manager</option>
                            </select>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" id="user_is_active"
                                value="1">
                            <label class="form-check-label" for="user_is_active">Konto aktywne</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-primary">Zapisz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editRestaurantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="restaurantForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_type" value="admin_restaurant_edit">

                    <div class="modal-header">
                        <h5 class="modal-title">Edytuj restaurację</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nazwa</label>
                                <input type="text" name="name" id="res_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Właściciel (Manager)</label>
                                <select name="user_id" id="res_user_id" class="form-select" required>
                                    <option value="" disabled selected>Wybierz managera...</option>
                                    @foreach ($managers as $manager)
                                        <option value="{{ $manager->id }}">
                                            {{ $manager->first_name }} {{ $manager->last_name }}
                                            ({{ $manager->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Wpisz imię, nazwisko lub email, aby filtrować listę.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Opis</label>
                            <textarea name="description" id="res_description" class="form-control" rows="3"></textarea>
                        </div>

                        <h6>Adres</h6>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Ulica</label>
                                <input type="text" name="street" id="res_street" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nr budynku</label>
                                <input type="text" name="building_number" id="res_building_number"
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Miasto</label>
                                <input type="text" name="city" id="res_city" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kod pocztowy</label>
                                <input type="text" name="postal_code" id="postal_code" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-primary">Zapisz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/postal_code.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    <script>
        window.adminConfig = {
            urls: {
                users: "{{ url('/admin/users') }}",
                restaurants: "{{ url('/restaurants') }}"
            },
            formErrorType: @if ($errors->any())
                "{{ old('form_type') }}"
            @else
                null
            @endif
        };
    </script>

</body>
