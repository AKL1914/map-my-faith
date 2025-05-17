@extends('layouts.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection
@section('content')
    <!-- Main Content -->
    <admin-event-pins></admin-event-pins>
@endsection

