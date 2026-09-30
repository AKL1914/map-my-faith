<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Maps') }}</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <style>
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
            font-family: var(--bs-body-font-family);
        }

        main {
            flex: 1;
        }

        .bg-gray {
            background-color: #808080;
        }

        .bg-blue {
            background-color: #4285f4;
        }

        .text-blue {
            color: #4285f4;
        }

        .auth-button,
        .google-btn {
            background-color: #4285f4;
            color: white;
            border: none;
            font-weight: 500;
            font-family: inherit;
            font-size: 1rem;
            min-height: 54px;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.625rem;
            width: 100%;
            max-width: 360px;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
            text-decoration: none;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .auth-button:hover,
        .google-btn:hover {
            background-color: #357ae8;
            color: white;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.16);
        }

        .auth-button:focus-visible,
        .google-btn:focus-visible {
            outline: 3px solid rgba(66, 133, 244, 0.35);
            outline-offset: 2px;
        }

        .google-btn i {
            font-size: 1.125rem;
        }

        .auth-form .form-control {
            min-height: 44px;
            padding: 0.55rem 0.75rem;
            border-color: #ced4da;
            border-radius: 0.5rem;
            font-family: inherit;
            font-size: 0.95rem;
        }

        .auth-form .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        .auth-form .auth-error {
            margin-bottom: 0.75rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }

        .auth-form .auth-error ul {
            padding-left: 1.1rem;
        }

        .auth-separator {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #6c757d;
            font-size: 0.85rem;
        }

        .auth-separator::before,
        .auth-separator::after {
            content: "";
            flex: 1;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Navigation -->
@include('partials.public.navigation')


<!-- Main Content -->
<main class="container text-center flex-grow-1 d-flex flex-column justify-content-center align-items-center">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mb-4 rounded-3" style="max-width: 200px;">

    <h1 class="mb-4 text-secondary">Welcome to {{ config('app.name') }}</h1>

    @auth
        <div id="app" class="w-100"></div> <!-- Vue mounts here -->
        <div class="mb-3 w-100 d-flex justify-content-center">
            <a href="{{ route('maps.index') }}" class="google-btn">
                <i class="fas fa-map-marker-alt me-2"></i>
                <span>View Maps</span>
            </a>
        </div>
    @else
        <div class="w-100 d-flex flex-column align-items-center">
            @if (session('status'))
                <div class="alert alert-success w-100" style="max-width: 360px;" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="auth-form text-start w-100 mb-3" style="max-width: 360px;">
                @csrf
                <h2 class="h5 mb-3 text-secondary text-center">Sign in with email</h2>

                @if ($errors->any())
                    <div class="alert alert-danger auth-error" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email" aria-label="Email" autocomplete="email" required autofocus>
                </div>

                <div class="mb-3">
                    <input id="password" type="password" name="password" class="form-control" placeholder="Password" aria-label="Password" autocomplete="current-password" required>
                </div>

                <button type="submit" class="auth-button">
                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                    <span>Sign in</span>
                </button>

                <div class="auth-separator my-3" aria-hidden="true">or</div>
            </form>

            <div class="mb-2 w-100 d-flex justify-content-center">
                <a href="{{ route('login.google') }}" class="google-btn">
                    <i class="bi bi-google"></i>
                    <span>Sign in with Google</span>
                </a>
            </div>

            <p class="mt-2">Don't have a Google account? <a href="{{ route('register') }}">Register here</a></p>
        </div>
    @endauth
</main>

@include('partials.footer')

</body>
</html>
