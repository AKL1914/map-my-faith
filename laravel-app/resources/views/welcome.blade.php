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
            font-family: 'Roboto', sans-serif;
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

        .google-btn {
            background-color: #4285f4;
            color: white;
            border: none;
            font-weight: 500;
            padding: 1rem 1.5rem;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            max-width: 360px;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .google-btn:hover {
            background-color: #357ae8;
        }

        .google-btn i {
            font-size: 1.5rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Navigation -->
@include('partials.public.navigation')


<!-- Main Content -->
<main class="container text-center flex-grow-1 d-flex flex-column justify-content-center align-items-center">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mb-4 rounded-3" style="max-width: 200px;">

    <h1 class="mb-4 text-blue">Welcome to {{ config('app.name') }}</h1>

    @auth
        <div id="app" class="w-100"></div> <!-- Vue mounts here -->
        <div class="mb-3 w-100 d-flex justify-content-center">
            <a href="{{ route('maps.index') }}" class="google-btn">
                <i class="fas fa-map-marker-alt me-2"></i>
                <span>View Maps</span>
            </a>
        </div>
    @else
        <div class="mb-3 w-100 d-flex justify-content-center">
            <a href="{{ route('login.google') }}" class="google-btn">
                <i class="bi bi-google"></i>
                <span>Sign in with Google</span>
            </a>
        </div>
    @endauth
</main>

@include('partials.footer')

</body>
</html>
