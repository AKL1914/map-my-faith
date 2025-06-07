<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center "  href="{{ url('/admin/dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-map-marker-alt me-2"></i>
        </div>
        <div class="sidebar-brand-text mx-3">{{ config('app.name', 'Laravel') }}</div>
    </a>
    @admin
    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/admin/dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link {{ request()->is('admin/campaigns') ? 'active' : '' }}" href="{{ url('/admin/campaigns') }}">
            <i class="fas fa-bullhorn"></i> <span>Campaigns</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->is('admin/pins') ? 'active' : '' }}" href="{{ url('/admin/pins') }}">
            <i class="fas fa-map-pin"></i> <span>Pins</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->is('admin/users') ? 'active' : '' }}" href="{{ url('/admin/users') }}">
            <i class="fas fa-users"></i> <span>Users</span>
        </a>
    </li>
    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed {{ request()->is('admin/events*') || request()->is('admin/game-pins*') ? 'active' : '' }}" href="#gamificationSubmenu"  data-toggle="collapse" data-target="#collapseTwo"
           aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-gamepad"></i>
            <span>Gamification</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Gamification:</h6>
                <a class="collapse-item" href="{{ url('/admin/events') }}">Events</a>
                <a class="collapse-item" href="{{ url('/admin/game-pins') }}">Game Pins</a>
            </div>
        </div>
    </li>
    @endadmin
    <!-- Divider -->
    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link {{ request()->is('maps') ? 'active' : '' }}" href="{{ url('/maps') }}">
            <i class="fas fa-map"></i> <span>Maps</span>
        </a>
    </li>
    @auth
    <!-- Divider -->
    <hr class="sidebar-divider">

    <li class="nav-item">
        <a class="nav-link {{ request()->is('user/' . auth()->id() . '/pins') ? 'active' : '' }}" href="{{ url('/user/' . auth()->id() . '/pins') }}">
            <i class="fas fa-map-pin"></i> <span>My Pins</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->is('leaderboard') ? 'active' : '' }}" href="{{ url('/leaderboard') }}">
            <i class="fas fa-award"></i> <span>Leaderboard</span>
        </a>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
    @endauth

</ul>
