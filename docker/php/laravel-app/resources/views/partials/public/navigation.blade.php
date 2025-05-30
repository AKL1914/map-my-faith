<nav class="navbar navbar-expand-lg navbar-dark bg-blue mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="fas fa-map-marker-alt me-2"></i>{{ config('app.name', 'Laravel') }}
        </a>

        <div class="d-flex">
            @auth
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
