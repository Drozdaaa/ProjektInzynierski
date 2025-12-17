@include('shared.html')
@include('shared.head', ['pageTitle' => 'Panel Administratora'])

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
        </div>

        <div class="table-responsive-sm">
            <div id="table-users">
                <table class="table table-striped align-middle text-center">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Imię</th>
                            <th scope="col">Nazwisko</th>
                            <th scope="col">Email</th>
                            <th scope="col">Numer telefonu</th>
                            <th scope="col">Rola</th>
                            <th scope="col">Status</th>
                            <th scope="col">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->role->name }}</td>
                                <td>
                                    @if ($user->is_active)
                                        <span class="badge bg-success">Aktywny</span>
                                    @else
                                        <span class="badge bg-danger">Nieaktywny</span>
                                    @endif
                                </td>
                                <td class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-info btn-sm"
                                        onclick="openUserModal({{ $user->id }}, '{{ $user->first_name }}', '{{ $user->last_name }}', '{{ $user->email }}', '{{ $user->phone }}', {{ $user->role_id }}, {{ $user->is_active }})">
                                        Edytuj
                                    </button>

                                    @if (!in_array($user->id, [1, 4]))
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika? Jeśli to manager, a lokal ma rezerwacje, konto zostanie tylko dezaktywowane.')">
                                                Usuń
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-secondary btn-sm" disabled
                                            title="Konto chronione">Usuń</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <th scope="row" colspan="8" class="text-center">Brak użytkowników.</th>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="table-restaurants" class="d-none">
                <table class="table table-striped align-middle text-center">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nazwa</th>
                            <th scope="col">Miejscowość</th>
                            <th scope="col">Ulica</th>
                            <th scope="col">Kod pocztowy</th>
                            <th scope="col">Właściciel</th>
                            <th scope="col">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($restaurants as $restaurant)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $restaurant->name }}</td>
                                <td>{{ $restaurant->address->city }}</td>
                                <td>{{ $restaurant->address->street }} {{ $restaurant->address->building_number }}
                                </td>
                                <td>{{ $restaurant->address->postal_code }}</td>
                                <td>{{ $restaurant->user->first_name }} {{ $restaurant->user->last_name }}</td>
                                <td class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-info btn-sm"
                                        onclick="openRestaurantModal({{ $restaurant->id }}, '{{ $restaurant->name }}', '{{ $restaurant->description }}', '{{ $restaurant->address->street }}', '{{ $restaurant->address->building_number }}', '{{ $restaurant->address->city }}', '{{ $restaurant->address->postal_code }}', {{ $restaurant->user_id }})">
                                        Edytuj
                                    </button>

                                    <form method="POST" action="{{ route('restaurants.destroy', $restaurant->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Na pewno usunąć ten lokal?')">
                                            Usuń</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <th scope="row" colspan="7" class="text-center">Brak lokali do wyświetlenia.</th>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                            <input type="text" name="first_name" id="user_first_name" class="form-control"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nazwisko</label>
                            <input type="text" name="last_name" id="user_last_name" class="form-control"
                                required>
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
                                <div class="form-text">Możesz wpisać imię, nazwisko lub email, aby wyszukać.</div>
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
                                <input type="text" name="postal_code" id="res_postal_code" class="form-control"
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

    <script>
        $(document).ready(function() {
            $('#res_user_id').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#editRestaurantModal'),
                width: '100%',
                placeholder: "Wybierz managera...",
                language: {
                    noResults: function() {
                        return "Brak wyników";
                    }
                }
            });
        });

        function showTable(type) {
            const usersTable = document.getElementById('table-users');
            const restaurantsTable = document.getElementById('table-restaurants');

            if (type === 'users') {
                usersTable.classList.remove('d-none');
                restaurantsTable.classList.add('d-none');
                localStorage.setItem('adminTab', 'users');
            } else {
                usersTable.classList.add('d-none');
                restaurantsTable.classList.remove('d-none');
                localStorage.setItem('adminTab', 'restaurants');
            }
        }

        function openUserModal(id, firstName, lastName, email, phone, roleId, isActive) {
            const form = document.getElementById('userForm');
            form.action = "{{ url('/admin/users') }}/" + id;

            document.getElementById('user_first_name').value = firstName;
            document.getElementById('user_last_name').value = lastName;
            document.getElementById('user_email').value = email;
            document.getElementById('user_phone').value = phone;
            document.getElementById('user_role_id').value = roleId;
            document.getElementById('user_is_active').checked = isActive == 1;

            var modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            modal.show();
        }

        function openRestaurantModal(id, name, desc, street, bNumber, city, pCode, userId) {
            const form = document.getElementById('restaurantForm');
            form.action = "{{ url('/restaurants') }}/" + id;

            document.getElementById('res_name').value = name;
            document.getElementById('res_description').value = desc || '';
            document.getElementById('res_street').value = street;
            document.getElementById('res_building_number').value = bNumber;
            document.getElementById('res_city').value = city;
            document.getElementById('res_postal_code').value = pCode;

            $('#res_user_id').val(userId).trigger('change');

            var modal = new bootstrap.Modal(document.getElementById('editRestaurantModal'));
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const activeTab = localStorage.getItem('adminTab') || 'users';
            if (activeTab === 'restaurants') {
                document.getElementById('btn-restaurants').click();
            }
            @if ($errors->any())
                @if (old('form_type') === 'admin_user_edit')
                    var modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    modal.show();
                @elseif (old('form_type') === 'admin_restaurant_edit')
                    var modal = new bootstrap.Modal(document.getElementById('editRestaurantModal'));
                    modal.show();
                @endif
            @endif
        });
    </script>
</body>
