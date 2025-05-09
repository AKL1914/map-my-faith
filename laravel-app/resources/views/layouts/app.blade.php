<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }
        .bg-gray {
            background-color: #808080; /* Gray background */
        }
    </style>

    @yield('head')
    <script>
        window.authUser = @json(auth()->user());
    </script>

</head>
<body>
@include('partials.navigation')
<div id="app">
    @yield('content')
</div>

@include('partials.footer')
</body>
</html>
