@extends('layouts.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection
@section('content')
    <h1 class="text-center">{{ $campaign->name }}</h1>
    <!-- Main Content -->
    <map-manager></map-manager>
    <script>
        window.campaign = @json($campaign);
    </script>
@endsection

