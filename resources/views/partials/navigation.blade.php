<nav class="navbar navbar-dark bg-dark px-3">
    <div class="d-flex gap-2">
        <a class="btn {{ request()->is('/') ? 'btn-light text-dark' : 'btn-outline-light' }}" href="{{ url('/') }}">Home</a>

        @admin
        <a class="btn {{ request()->is('admin/dashboard') ? 'btn-light text-dark' : 'btn-outline-light' }}" href="{{ url('/admin/dashboard') }}">Dashboard</a>
        <a class="btn {{ request()->is('admin/leaderboard') ? 'btn-light text-dark' : 'btn-outline-light' }}" href="{{ url('/admin/leaderboard') }}">Leaderboard</a>
        <a class="btn {{ request()->is('admin/campaigns') ? 'btn-light text-dark' : 'btn-outline-light' }}" href="{{ url('/admin/campaigns') }}">Campaigns</a>
        <a class="btn {{ request()->is('admin/users') ? 'btn-light text-dark' : 'btn-outline-light' }}" href="{{ url('/admin/users') }}">Users</a>
        @endadmin

        <a class="btn {{ request()->is('maps') ? 'btn-light text-dark' : 'btn-outline-light' }}" href="{{ url('/maps') }}">Maps</a>

        <div class="d-flex">
            @auth
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            @endauth
        </div>
    </div>
</nav>
