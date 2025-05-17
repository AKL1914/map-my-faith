@extends('layouts.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection
@section('content')
    <!-- Main Content -->
    <admin-game-pin-manager></admin-game-pin-manager>
@endsection

