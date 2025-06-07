@extends('layouts.theme.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.pinevent = @json($event);
    </script>
@endsection
@section('content')
    <!-- Main Content -->
    <admin-event-participants></admin-event-participants>
@endsection

