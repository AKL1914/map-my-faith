<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#808080">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        .bg-gray {
            background-color: #808080 !important;
        }

        .navbar .nav-link {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }

        .navbar .btn {
            font-size: 0.95rem;
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.25rem;
            }

            footer {
                font-size: 0.9rem;
            }
        }

        /* Add some custom styles to make it feel like a mobile app */
        body {
            font-family: 'Roboto', sans-serif;
        }

        h1 {
            font-size: 1.75rem;
            font-weight: bold;
        }

        .card {
            border-radius: 15px; /* Rounded corners for a mobile app feel */
        }

        .table th, .table td {
            padding: 0.8rem;
        }

        .table th {
            font-size: 1rem;
            font-weight: bold;
        }

        .table td {
            font-size: 0.95rem;
        }

        @media (max-width: 576px) {
            .table th, .table td {
                font-size: 0.85rem; /* Adjust font size for smaller screens */
            }
            h1 {
                font-size: 1.5rem;
            }
        }
        /* Custom gray background for the table header */
        .table thead {
            background-color: #f8f9fa; /* Light gray background */
        }

        /* Optional: Adjust text color for the table header */
        .table thead th {
            color: #495057; /* Darker text for contrast */
        }

        .bg-blue {
            background-color: #4285f4;
        }

    </style>

    @yield('head')
    <script>
        window.authUser = @json(auth()->user());
    </script>
</head>
<body>
@include('partials.navigation')

{{-- Main Content --}}
<main class="container my-3">
    <div id="app">
        @yield('content')
    </div>
</main>

@include('partials.footer')
</body>
</html>


