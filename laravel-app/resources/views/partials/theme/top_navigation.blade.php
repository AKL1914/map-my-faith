<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->name }}</span>
                <img class="img-profile rounded-circle"
                     src="{{ asset('theme/img/undraw_profile.svg') }}">
            </a>
            <!-- Dropdown - User Information -->
            @auth
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                     aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="{{ route('profile.edit') }}" >
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Profile
                    </a>

                    @admin
                    <!-- Horizon (Laravel Queue Monitor) -->
                    <a class="dropdown-item" href="/horizon" target="_blank">
                        <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Horizon
                    </a>

                    <!-- Settings -->
                    <a class="dropdown-item" href="/admin/settings/manage">
                        <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                        Settings
                    </a>
                    @endadmin
                    <div class="dropdown-divider"></div>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="dropdown-item" href="#" data-toggle="modal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </button>
                    </form>
                </div>
            @endauth
        </li>

    </ul>

</nav>
