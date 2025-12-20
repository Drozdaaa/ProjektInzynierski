<div id="table-users">
    <div class="card mb-4 bg-light">
        <div class="card-body">
            <form action="{{ route('users.admin-dashboard') }}" method="GET" class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label fw-bold">Szukaj (Imię, Nazwisko, Email)</label>
                    <input type="text" name="user_search" class="form-control" value="{{ request('user_search') }}"
                        placeholder="Wpisz frazę...">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Rola</label>
                    <select name="role_id" class="form-select">
                        <option value="">Wszystkie</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected(request('role_id') == $role->id)>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Status</label>
                    <select name="user_status" class="form-select">
                        <option value="">Wszystkie</option>
                        <option value="1" @selected(request('user_status') == '1')>Aktywny</option>
                        <option value="0" @selected(request('user_status') == '0')>Nieaktywny</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"
                        onclick="localStorage.setItem('adminTab', 'users')">
                        Filtruj
                    </button>
                    <a href="{{ route('users.admin-dashboard') }}" class="btn btn-outline-secondary"
                        onclick="localStorage.setItem('adminTab', 'users')">
                        Wyczyść
                    </a>
                </div>
            </form>
        </div>
    </div>

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
                    <th scope="row">{{ $users->firstItem() + $loop->index }}</th>
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
                                    onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika?')">
                                    Usuń
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary btn-sm" disabled title="Konto chronione">Usuń</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <th scope="row" colspan="8" class="text-center">Brak użytkowników spełniających kryteria.
                    </th>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
