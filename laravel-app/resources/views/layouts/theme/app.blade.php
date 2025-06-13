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

    <style>
        .map-outer-container {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
    </style>

</head>

<body id="page-top" class="sidebar-toggled">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    @include('partials.theme.navigation')

    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            @include('partials.theme.top_navigation')
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid {{ request()->is('maps') ? 'map-outer-container' : '' }}">
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
