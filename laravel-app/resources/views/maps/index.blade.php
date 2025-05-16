@extends('layouts.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.campaign = @json($campaign);
    </script>
@endsection
@section('content')

    <h1 class="text-center text-secondary">{{ $campaign->name }}</h1>

    <!-- Main Content -->
    <map-manager></map-manager>

@endsection

