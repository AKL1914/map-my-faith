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
            font-family: 'Roboto', sans-serif;
        }

        main {
            flex: 1;
        }
        .bg-gray {
            background-color: #808080; /* Gray background */
        }

        .bg-blue {
            background-color: #4285f4;
        }
    </style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

@include('partials.public.navigation')

<div class="container text-center flex-grow-1 d-flex flex-column justify-content-center align-items-center">
    <i class="fas fa-spinner fa-spin fa-5x text-secondary mb-4"></i>

    <h1 class="mb-4">Thank You!</h1>
    <p class="lead">Thank you for participating. The Administrator will activate your account.</p>
    <p class="text-muted">You will be redirected in 5 seconds...</p>
</div>

@include('partials.footer')

<script>
    // Redirect to root route after 5 seconds
    setTimeout(function() {
        window.location.href = '/';
    }, 5000);
</script>

</body>
</html>

