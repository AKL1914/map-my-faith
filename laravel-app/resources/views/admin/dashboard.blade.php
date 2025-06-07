@extends('layouts.theme.app')
@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection
@section('content')
    <admin-dashboard></admin-dashboard>
@endsection

