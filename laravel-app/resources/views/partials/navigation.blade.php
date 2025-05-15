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
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                        <i class="fas fa-home"></i> <span class="d-none d-sm-inline">Home</span>
                    </a>
                </li>

                @admin
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ url('/admin/dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> <span class="d-none d-sm-inline">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/leaderboard') ? 'active' : '' }}" href="{{ url('/admin/leaderboard') }}">
                        <i class="fas fa-award"></i> <span class="d-none d-sm-inline">Leaderboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/campaigns') ? 'active' : '' }}" href="{{ url('/admin/campaigns') }}">
                        <i class="fas fa-bullhorn"></i> <span class="d-none d-sm-inline">Campaigns</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/pins') ? 'active' : '' }}" href="{{ url('/admin/pins') }}">
                        <i class="fas fa-map-pin"></i> <span class="d-none d-sm-inline">Pins</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/users') ? 'active' : '' }}" href="{{ url('/admin/users') }}">
                        <i class="fas fa-users"></i> <span class="d-none d-sm-inline">Users</span>
                    </a>
                </li>
                @endadmin

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('maps') ? 'active' : '' }}" href="{{ url('/maps') }}">
                        <i class="fas fa-map"></i> <span class="d-none d-sm-inline">Maps</span>
                    </a>
                </li>
            </ul>


            @auth
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-light btn-sm me-2" title="Edit Profile">
                    <i class="fas fa-user"></i>
                </a>

                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            @endauth


        </div>
    </div>
</nav>
