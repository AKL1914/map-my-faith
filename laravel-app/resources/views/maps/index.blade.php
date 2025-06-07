@extends('layouts.theme.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.campaign = @json($campaign);
    </script>
@endsection
@section('content')
    <!-- Main Content -->
    <map-manager></map-manager>

@endsection

