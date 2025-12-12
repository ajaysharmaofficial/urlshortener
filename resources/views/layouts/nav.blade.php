{{-- resources/views/partials/navbar.blade.php --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">

        <a class="navbar-brand fw-bold" href="{{ route('dashboard') ?? url('/') }}">
            <i class="bi bi-link-45deg"></i> URL Shortener
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto">
                @role('SuperAdmin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}"
                            href="{{ route('companies.index') }}">
                            <i class="bi bi-building"></i> Companies
                        </a>
                    </li>
                @endrole

                @role('Admin|Member|SuperAdmin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('urls.*') ? 'active' : '' }}"
                            href="{{ route('urls.index') }}">
                            <i class="bi bi-link"></i> URLs
                        </a>
                    </li>
                @endrole

                @role('Admin|SuperAdmin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('urls.*') ? 'active' : '' }}"
                            href="{{ route('invite.index') }}">
                            <i class="bi bi-person-plus"></i> Invites
                        </a>
                    </li>
                @endrole
            </ul>

            <ul class="navbar-nav ms-auto align-items-center">
                @auth
                    @if (optional(auth()->user()->company)->name)
                        <li class="nav-item me-2">
                            <span class="badge bg-secondary px-3 py-2">
                                <i class="bi bi-building"></i>
                                {{ Str::limit(auth()->user()->company->name, 18) }}
                            </span>
                        </li>
                    @endif

                    <li class="nav-item me-2">
                        <span class="nav-link text-light">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </span>
                    </li>

                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-light px-3">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>