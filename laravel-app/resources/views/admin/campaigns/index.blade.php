@extends('layouts.theme.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection
@section('content')
    <!-- Main Content -->
    <campaign-manager></campaign-manager>
@endsection

