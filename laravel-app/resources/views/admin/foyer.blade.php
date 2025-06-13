@extends('layouts.theme.app')

@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-bar text-primary mr-2"></i>
                Dashboard: Foyer
            </h1>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <!-- Total Pamphlets -->
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Pamphlets Distributed
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPins }}</div>
                    </div>
                </div>
            </div>

            <!-- Brethren Participated -->
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Brethren Participated
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalParticipants }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Tables -->
        <div class="row mb-4">
            <!-- Area Participation Table -->
            <div class="col-xl-6 mb-4">
                <div class="card shadow">
                    <div class="card-header font-weight-bold text-primary">
                        <i class="fas fa-map-marker-alt mr-1"></i> Pamphlet Distribution by Area
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th>Area</th>
                                <th>Participation</th>
                                <th>Pamphlets Distributed</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($areasData as $area => $count)
                                <tr>
                                    <td>Area{{ $area }}</td>
                                    <td>{{ number_format($count['percentage']) }}%</td>
                                    <td>{{ number_format($count['pin_count']) }}</td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Distributors Table -->
            <div class="col-xl-6 mb-4">
                <div class="card shadow">
                    <div class="card-header font-weight-bold text-primary">
                        <i class="fas fa-user-friends mr-1"></i> Top Brethren Distributors
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Pamphlets</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($topPins as $index => $pin)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $pin->name }}</td>
                                    <td>{{ number_format($pin->pinCount) }}</td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Message + QR Code -->
        <div class="row">
            <div class="col-xl-8">
                <div class="card bg-light shadow p-4">
                    <h4 class="text-primary font-weight-bold mb-1">Help us in</h4>
                    <h3 class="text-primary font-weight-bold">Sharing our faith!</h3>
                    <p class="mt-2 text-gray-700">
                        Every pamphlet shared is a step toward reaching a soul. Join us in spreading hope, one message at a time.
                    </p>
                </div>
            </div>
            <div class="col-xl-4 d-flex justify-content-center align-items-center">
                <img src="{{ asset('images/qr.png') }}" alt="QR Code" style="width: 150px;">
            </div>
        </div>
    </div>
@endsection
