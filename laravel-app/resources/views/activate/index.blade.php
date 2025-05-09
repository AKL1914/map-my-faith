<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank You - Map My Faith</title>

    <!-- Bootstrap 5 CSS CDN -->
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
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-gray mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Map My Faith</a>
    </div>
</nav>

<div class="container text-center flex-grow-1 d-flex flex-column justify-content-center align-items-center">
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
```
