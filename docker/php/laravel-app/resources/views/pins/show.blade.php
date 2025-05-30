@extends('layouts.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection
@section('content')
    <!-- Main Content -->
    <pin-viewer></pin-viewer>
    <script>
        window.pin = @json($pin);
    </script>
@endsection

