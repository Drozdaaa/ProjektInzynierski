<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('main.index') }}">Navbar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01"
            aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('main.index') ? 'active' : '' }}"
                        href="{{ route('main.index') }}">
                        Home
                        @if (request()->routeIs('main.index'))
                            <span class="visually-hidden">(current)</span>
                        @endif
                    </a>
                </li>
                @can('admin-or-manager')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Menadżer
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('users.manager-dashboard') }}">Wydarzenia</a></li>
                            <li><a class="dropdown-item" href="{{ route('menus.index') }}">Zarządzaj menu</a></li>
                            <li><a class="dropdown-item" href="{{ route('restaurants.index') }}">Informacje o lokalu</a>
                            </li>
                            @if (!\App\Models\Restaurant::where('user_id', auth()->id())->exists())
                                <li><a class="dropdown-item" href="{{ route('restaurants.create') }}">Utwórz lokal</a></li>
                            @endif
                        </ul>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.user-dashboard') ? 'active' : '' }}"
                            href="{{ route('users.user-dashboard') }}">
                            Twoje wydarzenia
                        </a>
                    </li>
                    </li>
                @endcan
                @can('is-admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.admin-dashboard') ? 'active' : '' }}"
                            href="{{ route('users.admin-dashboard') }}">
                            Administrator
                        </a>
                    </li>
                @endcan
                @can('is-user')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.user-dashboard') ? 'active' : '' }}"
                            href="{{ route('users.user-dashboard') }}">
                            Twoje wydarzenia
                        </a>
                    </li>
                @endcan

            </ul>
            <ul id="navbar-user" class="navbar-nav mb-2 mb-lg-0">
                @if (Auth::check())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.edit') ? 'active' : '' }}"
                            href="{{ route('users.edit', Auth::id()) }}">
                            Profil {{ Auth::user()->name }}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-danger" href="{{ route('logout') }}">
                            Wyloguj się
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}"
                            href="{{ route('login', !request()->routeIs('login') && !request()->routeIs('register') ? ['redirect_to' => url()->full()] : []) }}">
                            Zaloguj się
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}"
                            href="{{ route('register') }}">
                            Zarejestruj się
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
