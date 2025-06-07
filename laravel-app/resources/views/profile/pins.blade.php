@extends('layouts.theme.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.userId = @json($userId);
    </script>
@endsection
@section('content')
    <!-- Main Content -->

    <user-pins-viewer></user-pins-viewer>

@endsection

