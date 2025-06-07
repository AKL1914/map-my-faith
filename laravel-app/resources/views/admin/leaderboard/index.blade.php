@extends('layouts.theme.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-trophy text-warning"></i> Leaderboard</h1>

    {{-- Top 10 Users --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users"></i> Top 10 Users</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered">
                    <thead class="thead-light">
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
                            <td><i class="fas fa-crown {{ $index === 0 ? 'text-warning' : '' }}"></i> {{ $index + 1 }}</td>
                            <td>{{ $entry->name }}</td>
                            <td>{{ $entry->area ?? 'N/A' }}</td>
                            <td>{{ $entry->pinCount }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top Suburbs --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt"></i> Top Suburb Legends</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered">
                    <thead class="thead-light">
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
                            <td>{{ $index + 1 }}</td>
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

    {{-- Top Areas --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map"></i> Top Areas (By User)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered">
                    <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Area</th>
                        <th>Pins</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($topAreas as $index => $area)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $area->area ?? 'N/A' }}</td>
                            <td>{{ $area->pinCount }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
