@extends('layouts.app')
@section('head')
    @vite('resources/js/app.js') <!-- Important for Vue -->
@endsection
@section('content')
    <main class="container text-center my-1 flex-grow-1">
        <div id="app"></div> <!-- Vue mounts here -->
    </main>
@endsection
