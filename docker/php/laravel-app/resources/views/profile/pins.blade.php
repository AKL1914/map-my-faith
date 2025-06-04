@extends('layouts.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection
@section('content')
    <!-- Main Content -->
    <user-pins-viewer></user-pins-viewer>
    <script>
        window.userId = @json($userId);
    </script>
@endsection

