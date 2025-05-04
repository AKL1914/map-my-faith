@extends('layouts.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/admindashboard.js'])
@endsection
@section('content')
    <!-- Main Content -->
    <div class="container mt-4">
        <div id="app"></div> <!-- Vue app mounts here -->
    </div>
@endsection

