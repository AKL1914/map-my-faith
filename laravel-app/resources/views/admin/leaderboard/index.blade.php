@extends('layouts.app')
@section('content')
    <!-- Main Content -->
    <main class="container my-5 flex-grow-1">
        <h1 class="text-center mb-4">Leaderboard</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped shadow-sm bg-white">
                <thead class="table-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Pins</th>
                </tr>
                </thead>
                <tbody>
                @foreach($leaderboardData as $index => $entry)
                    <tr>
                        <th scope="row">{{ $index + 1 }}</th>
                        <td>{{ $entry->name }}</td>
                        <td>{{ $entry->pinCount }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </main>
@endsection
