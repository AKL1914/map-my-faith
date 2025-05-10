{{-- Navigation --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-blue px-3 sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="fas fa-map-marker-alt me-2"></i>{{ config('app.name', 'Laravel') }}
        </a>


        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMobile"
                aria-controls="navbarMobile" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMobile">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
                </li>

                @admin
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ url('/admin/dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/leaderboard') ? 'active' : '' }}" href="{{ url('/admin/leaderboard') }}">Leaderboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/campaigns') ? 'active' : '' }}" href="{{ url('/admin/campaigns') }}">Campaigns</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/pins') ? 'active' : '' }}" href="{{ url('/admin/pins') }}">Pins</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/users') ? 'active' : '' }}" href="{{ url('/admin/users') }}">Users</a>
                </li>
                @endadmin

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('maps') ? 'active' : '' }}" href="{{ url('/maps') }}">Maps</a>
                </li>
            </ul>

            @auth
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            @endauth
        </div>
    </div>
</nav>
