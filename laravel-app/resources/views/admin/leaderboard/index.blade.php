@extends('layouts.app')

@section('content')
    <!-- Main Content -->
    <main class="container my-5 px-3">
        <h1 class="text-center mb-4">
            <i class="fas fa-trophy"></i> Leaderboard
        </h1>

        <div class="table-responsive">
            <div class="card shadow-sm bg-white">
                <div class="card-body">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="bg-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Pins</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($leaderboardData as $index => $entry)
                            <tr>
                                <th scope="row">
                                    <i class="fas fa-crown"></i> {{ $index + 1 }}
                                </th>
                                <td>
                                    <i class="fas fa-user"></i> {{ $entry->name }}
                                </td>
                                <td>
                                    <i class="fas fa-map-pin"></i> {{ $entry->pinCount }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection
