@extends('layouts.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
{{--    <link rel="preload" as="style" href="https://app.mapmyfaith.co.nz/build/assets/app-CrhXXqm8.css" />--}}
{{--    <link rel="modulepreload" href="https://app.mapmyfaith.co.nz/build/assets/app-l0sNRNKZ.js" />--}}
{{--    <link rel="modulepreload" href="https://app.mapmyfaith.co.nz/build/assets/app-Cd5jHaUO.js" />--}}
{{--    <link rel="stylesheet" href="https://app.mapmyfaith.co.nz/build/assets/app-CrhXXqm8.css" />--}}
{{--    <script type="module" src="https://app.mapmyfaith.co.nz/build/assets/app-l0sNRNKZ.js"></script>--}}
{{--    <script type="module" src="https://app.mapmyfaith.co.nz/build/assets/app-Cd5jHaUO.js"></script>--}}
    <script>
        window.campaign = @json($campaign);
    </script>
@endsection
@section('content')

    <h1 class="text-center text-secondary">{{ $campaign->name }}</h1>

    <!-- Main Content -->
    <map-manager></map-manager>

@endsection

