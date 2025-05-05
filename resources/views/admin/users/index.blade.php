@extends('layouts.app')
@section('head')
    <!-- Vite compiled files -->
    @vite(['resources/css/app.css', 'resources/js/users.js'])
@endsection
@section('content')
    <!-- Main Content -->
    <div class="container mt-4">
        <div id="app"></div> <!-- Vue app mounts here -->
    </div>
@endsection
