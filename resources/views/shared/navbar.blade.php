<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor01">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('main.index') ? 'active' : '' }}"
             href="{{ route('main.index') }}">
             Home
             @if(request()->routeIs('main.index'))<span class="visually-hidden">(current)</span>@endif
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('users.manager-dashboard') ? 'active' : '' }}"
             href="{{ route('users.manager-dashboard') }}">
             Menadżer
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('users.admin-dashboard') ? 'active' : '' }}"
             href="{{ route('users.admin-dashboard') }}">
             Administrator
          </a>
        </li>
      </ul>
      <form class="d-flex">
        <input class="form-control me-sm-2" type="search" placeholder="Search">
        <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
      </form>
      <ul id="navbar-user" class="navbar-nav mb-2 mb-lg-0">
            @if (Auth::check())
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('logout') ? 'active' : '' }}"
                       href="{{ route('logout') }}">
                       {{ Auth::user()->name }} (wyloguj się)
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}"
                       href="{{ route('login') }}">
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
