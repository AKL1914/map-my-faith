<!-- Navbar (non-collapsible) -->
<nav class="navbar navbar-dark bg-dark px-3">
    <div class="d-flex gap-2">
        <a class="btn btn-outline-light" href="{{ url('/') }}">Home</a>
        <a class="btn btn-outline-light" href="{{ url('/admin/dashboard') }}">Dashboard</a>
        <a class="btn btn-outline-light" href="{{ url('/admin/leaderboard') }}">Leaderboard</a>
        <a class="btn btn-outline-light" href="{{ url('/admin/campaigns') }}">Campaigns</a>
        <a class="btn btn-outline-light" href="{{ url('/maps') }}">Maps</a>
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
