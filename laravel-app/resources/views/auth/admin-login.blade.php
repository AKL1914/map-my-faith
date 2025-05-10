@extends('layouts.app')

@section('content')
    <div class="container mt-5 d-flex justify-content-center align-items-center">
        <div class="w-100" style="max-width: 400px; border: 1px solid #ddd; border-radius: 8px; padding: 30px; background-color: #f9f9f9;">
            <h2 class="mb-4 text-center"><i class="fas fa-user-shield me-2"></i>Admin Login</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input
                            type="email"
                            class="form-control form-control-lg"
                            id="email"
                            name="email"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input
                            type="password"
                            class="form-control form-control-lg"
                            id="password"
                            name="password"
                            required
                        >
                    </div>
                </div>


                <!-- Larger Login Button -->
                <button type="submit" class="btn btn-primary w-100 py-3 btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>
        </div>
    </div>
@endsection
