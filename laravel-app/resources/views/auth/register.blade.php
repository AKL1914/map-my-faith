<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - {{ config('app.name', 'Maps') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .bg-blue {
            background-color: #4285f4;
        }

        .register-form {
            width: 100%;
            max-width: 360px;
        }

        .register-form .form-control {
            min-height: 44px;
            padding: 0.55rem 0.75rem;
            border-color: #ced4da;
            border-radius: 0.5rem;
            font-family: inherit;
            font-size: 0.95rem;
        }

        .register-form .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        .register-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 54px;
            padding: 0.75rem 1.25rem;
            border: 0;
            border-radius: 0.5rem;
            background-color: #4285f4;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
            color: #fff;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .register-button:hover {
            background-color: #357ae8;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.16);
            color: #fff;
        }

        .register-button:focus-visible {
            outline: 3px solid rgba(66, 133, 244, 0.35);
            outline-offset: 2px;
        }

        .register-error {
            margin-bottom: 0.75rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }

        .register-error ul {
            padding-left: 1.1rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    @include('partials.public.navigation')

    <main class="container text-center flex-grow-1 d-flex flex-column justify-content-center align-items-center py-4">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mb-4 rounded-3" style="max-width: 200px;">
        <h1 class="h3 mb-2 text-secondary">Create an account</h1>
        <p class="text-muted mb-4">Your account will need administrator activation before you can sign in.</p>

        <form method="POST" action="{{ route('register.submit') }}" class="register-form text-start">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger register-error" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-3">
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="form-control" placeholder="Name" aria-label="Name" autocomplete="name" required autofocus>
            </div>
            <div class="mb-3">
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control" placeholder="Email" aria-label="Email" autocomplete="email" required>
            </div>
            <div class="mb-3">
                <input id="password" name="password" type="password" class="form-control" placeholder="Password (at least 8 characters)" aria-label="Password (at least 8 characters)" autocomplete="new-password" required>
            </div>
            <div class="mb-3">
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="Confirm password" aria-label="Confirm password" autocomplete="new-password" required>
            </div>
            <button type="submit" class="register-button">Register</button>
        </form>

        <p class="mt-3 mb-0">Already have an account? <a href="{{ route('home') }}">Sign in</a></p>
    </main>

    @include('partials.footer')
</body>
</html>
