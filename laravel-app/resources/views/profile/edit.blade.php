@extends('layouts.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection
@section('content')
    <!-- Main Content -->
    <profile-viewer></profile-viewer>
    <script>
        window.user = @json($user);
        window.areaGroups = @json($areaGroups);
        window.pinCount = @json($pinCount);
    </script>
@endsection

