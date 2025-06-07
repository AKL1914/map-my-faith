{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">--}}
{{--    <meta name="csrf-token" content="{{ csrf_token() }}">--}}
{{--    <meta name="vapid-key" content="{{ config('webpush.vapid.public_key') }}">--}}
{{--    <meta name="theme-color" content="#808080">--}}
{{--    <title>{{ config('app.name', 'Laravel') }}</title>--}}

{{--    <!-- Bootstrap 5 CDN -->--}}
{{--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">--}}


{{--    <!-- Bootstrap Icons CDN -->--}}
{{--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">--}}
{{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">--}}


{{--    <style>--}}
{{--        html, body {--}}
{{--            height: 100%;--}}
{{--            margin: 0;--}}
{{--            padding: 0;--}}
{{--        }--}}

{{--        body {--}}
{{--            display: flex;--}}
{{--            flex-direction: column;--}}
{{--        }--}}

{{--        main {--}}
{{--            flex: 1;--}}
{{--        }--}}

{{--        .bg-gray {--}}
{{--            background-color: #808080 !important;--}}
{{--        }--}}

{{--        .navbar .nav-link {--}}
{{--            padding: 0.75rem 1rem;--}}
{{--            font-size: 1rem;--}}
{{--        }--}}

{{--        .navbar .btn {--}}
{{--            font-size: 0.95rem;--}}
{{--        }--}}

{{--        @media (max-width: 576px) {--}}
{{--            .navbar-brand {--}}
{{--                font-size: 1.25rem;--}}
{{--            }--}}

{{--            footer {--}}
{{--                font-size: 0.9rem;--}}
{{--            }--}}
{{--        }--}}

{{--        /* Add some custom styles to make it feel like a mobile app */--}}
{{--        body {--}}
{{--            font-family: 'Roboto', sans-serif;--}}
{{--        }--}}

{{--        h1 {--}}
{{--            font-size: 1.75rem;--}}
{{--            font-weight: bold;--}}
{{--        }--}}

{{--        .card {--}}
{{--            border-radius: 15px; /* Rounded corners for a mobile app feel */--}}
{{--        }--}}

{{--        .table th, .table td {--}}
{{--            padding: 0.8rem;--}}
{{--        }--}}

{{--        .table th {--}}
{{--            font-size: 1rem;--}}
{{--            font-weight: bold;--}}
{{--        }--}}

{{--        .table td {--}}
{{--            font-size: 0.95rem;--}}
{{--        }--}}

{{--        @media (max-width: 576px) {--}}
{{--            .table th, .table td {--}}
{{--                font-size: 0.85rem; /* Adjust font size for smaller screens */--}}
{{--            }--}}
{{--            h1 {--}}
{{--                font-size: 1.5rem;--}}
{{--            }--}}
{{--        }--}}
{{--        /* Custom gray background for the table header */--}}
{{--        .table thead {--}}
{{--            background-color: #f8f9fa; /* Light gray background */--}}
{{--        }--}}

{{--        /* Optional: Adjust text color for the table header */--}}
{{--        .table thead th {--}}
{{--            color: #495057; /* Darker text for contrast */--}}
{{--        }--}}

{{--        .bg-blue {--}}
{{--            background-color: #4285f4;--}}
{{--        }--}}

{{--        .nav-link {--}}
{{--            color: white !important;--}}
{{--        }--}}



{{--    </style>--}}

{{--    @yield('head')--}}
{{--    <script>--}}
{{--        window.authUser = @json(auth()->user());--}}
{{--    </script>--}}
{{--</head>--}}
{{--<body>--}}
{{--@include('partials.navigation')--}}

{{-- Main Content --}}
{{--<main class="container my-3">--}}
{{--    <div id="app">--}}
{{--        @yield('content')--}}
{{--    </div>--}}
{{--</main>--}}

{{--@include('partials.footer')--}}
{{--</body>--}}
{{--</html>--}}

    <!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="vapid-key" content="{{ config('webpush.vapid.public_key') }}">
    <title>{{ config('app.name', 'Maps') }}</title>


    <!-- Custom fonts for this template-->
    <link href="{{ asset('theme/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('theme/css/sb-admin-2.min.css')}}" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    @yield('head')
    <script>
        window.authUser = @json(auth()->user());
    </script>

</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center "  href="{{ url('/admin/dashboard') }}">
            <div class="sidebar-brand-icon">
                <i class="fas fa-map-marker-alt me-2"></i>
            </div>
            <div class="sidebar-brand-text mx-3">{{ config('app.name', 'Laravel') }}</div>
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('/admin/dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/campaigns') ? 'active' : '' }}" href="{{ url('/admin/campaigns') }}">
                <i class="fas fa-bullhorn"></i> <span>Campaigns</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/pins') ? 'active' : '' }}" href="{{ url('/admin/pins') }}">
                <i class="fas fa-map-pin"></i> <span>Pins</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/users') ? 'active' : '' }}" href="{{ url('/admin/users') }}">
                <i class="fas fa-users"></i> <span>Users</span>
            </a>
        </li>
        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed {{ request()->is('admin/events*') || request()->is('admin/game-pins*') ? 'active' : '' }}" href="#gamificationSubmenu"  data-toggle="collapse" data-target="#collapseTwo"
               aria-expanded="true" aria-controls="collapseTwo">
                <i class="fas fa-gamepad"></i>
                <span>Gamification</span>
            </a>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Gamification:</h6>
                    <a class="collapse-item" href="{{ url('/admin/events') }}">Events</a>
                    <a class="collapse-item" href="{{ url('/admin/game-pins') }}">Game Pins</a>
                </div>
            </div>
        </li>
        <!-- Divider -->
        <hr class="sidebar-divider">

        <li class="nav-item">
            <a class="nav-link {{ request()->is('maps') ? 'active' : '' }}" href="{{ url('/maps') }}">
                <i class="fas fa-map"></i> <span>Maps</span>
            </a>
        </li>
        <!-- Divider -->
        <hr class="sidebar-divider">

        <li class="nav-item">
            <a class="nav-link {{ request()->is('user/' . auth()->id() . '/pins') ? 'active' : '' }}" href="{{ url('/user/' . auth()->id() . '/pins') }}">
                <i class="fas fa-map-pin"></i> <span>My Pins</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->is('leaderboard') ? 'active' : '' }}" href="{{ url('/leaderboard') }}">
                <i class="fas fa-award"></i> <span>Leaderboard</span>
            </a>
        </li>
        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            @include('partials.theme.navigation')
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <div id="app">
                        @yield('content')
                </div>

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        @include('partials.theme.footer')

        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Bootstrap core JavaScript-->
<script src="{{ asset('/theme/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('/theme/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Core plugin JavaScript-->
<script src="{{ asset('/theme/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

<!-- Custom scripts for all pages-->
<script src="{{ asset('/theme/js/sb-admin-2.min.js') }} "></script>

</body>

</html>
