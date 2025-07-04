@extends('layouts.theme.app')

@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Refresh the page every 5 minutes (300,000 milliseconds)
        setTimeout(() => {
            window.location.reload();
        }, 300000);
    </script>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <!-- Top Summary Section -->
    <div class="row mb-4 text-center">
        <div class="col-md-6 mb-3">
            <div class="card shadow border-left-primary py-4">
                <div class="card-body">
                    <h5 class="text-primary text-uppercase mb-2">Pamphlets Distributed</h5>
                    <h2 class="display-4 text-gray-800 font-weight-bold">{{ $totalPins }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow border-left-success py-4">
                <div class="card-body">
                    <h5 class="text-success text-uppercase mb-2">Brethren Participated</h5>
                    <h2 class="display-4 text-gray-800 font-weight-bold">{{ $totalParticipants }}</h2>
                </div>
            </div>
        </div>
    </div>


    <!-- Carousel and Area Table Side by Side -->
    <div class="row mb-4">
        <div class="col-lg-3 mb-3">
            <div class="card shadow h-100">
                <div class="card-header font-weight-bold text-primary">
                    <i class="fas fa-map-marker-alt mr-1"></i> Pamphlet Distribution by Area
                </div>
                <div class="card-body table-responsive p-2">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Area</th>
                                <th>Pamphlets</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($areasData as $area => $count)
                            <tr>
                                <td>Area{{ $area }}</td>
                                <td>{{ number_format($count['pin_count']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-9 mb-3">
            <div id="photoCarousel" class="carousel slide" data-ride="carousel" data-interval="2000" data-pause="false">
                <div class="carousel-inner rounded shadow" style="max-height: 430px;">
                    @forelse($carouselPhotos as $photo)
                        <div class="carousel-item @if($loop->first) active @endif">
                            <img src="{{ $photo->url }}" class="d-block w-100" alt="Photo {{ $loop->iteration }}">
                        </div>
                    @empty
                        <div class="carousel-item active d-flex align-items-center justify-content-center" style="background-color: #f8f9fa; height: 200px;">
                            <span class="text-muted">No photos available</span>
                        </div>
                    @endforelse
                </div>
                <a class="carousel-control-prev" href="#photoCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
                </a>
                <a class="carousel-control-next" href="#photoCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
                </a>
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
