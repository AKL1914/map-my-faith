@extends('layouts.app')

@section('content')
    <main class="container my-5 px-3">
        <h1 class="text-center mb-4">
            <i class="fas fa-trophy"></i> Leaderboard
        </h1>

        <!-- Top Users Table -->
        <div class="table-responsive mb-5">
            <div class="card shadow-sm bg-white">
                <div class="card-body">
                    <h2 class="text-center mb-4">Top 10 Users</h2>
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Area</th>
                            <th>Pins</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($leaderboardData as $index => $entry)
                            <tr>
                                <th><i class="fas fa-crown"></i> {{ $index + 1 }}</th>
                                <td><i class="fas fa-user"></i> {{ $entry->name }}</td>
                                <td><i class="fas fa-map"></i> {{ $entry->area ?? 'N/A' }}</td>
                                <td><i class="fas fa-map-pin"></i> {{ $entry->pinCount }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Suburbs Table -->
        <div class="table-responsive mb-5">
            <div class="card shadow-sm bg-white">
                <div class="card-body">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-map-marker-alt"></i> Top Suburb Legends
                    </h2>
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Suburb</th>
                            <th>Total Pins</th>
                            <th>Top User</th>
                            <th>User's Pins in Suburb</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($topSuburbs as $index => $suburb)
                            <tr>
                                <th><i class="fas fa-map-marker-alt"></i> {{ $index + 1 }}</th>
                                <td>{{ $suburb->suburb }}</td>
                                <td>{{ $suburb->totalPins }}</td>
                                <td>{{ $suburb->user_name ?? 'N/A' }}</td>
                                <td>{{ $suburb->userPins ?? 0 }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Areas Table -->
        <div class="table-responsive mb-5">
            <div class="card shadow-sm bg-white">
                <div class="card-body">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-map"></i> Top Areas (By User)
                    </h2>
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Area</th>
                            <th>Pins</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($topAreas as $index => $area)
                            <tr>
                                <th><i class="fas fa-map"></i> {{ $index + 1 }}</th>
                                <td>{{ $area->area ?? 'N/A' }}</td>
                                <td>{{ $area->pinCount }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection
