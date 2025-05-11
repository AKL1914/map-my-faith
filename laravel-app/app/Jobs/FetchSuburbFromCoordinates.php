<?php
namespace App\Jobs;

use App\Models\Pin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class FetchSuburbFromCoordinates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pinId;

    public function __construct($pinId)
    {
        $this->pinId = $pinId;
    }

    public function handle(): void
    {
        $pin = Pin::find($this->pinId);

        if (!$pin) {
            return;
        }

        $lat = $pin->latitude;
        $lng = $pin->longitude;

        $response = Http::withHeaders([
            'User-Agent' => 'maps/1.0 (villamornatonio@gmail.com)' // Set the correct User-Agent header
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'format' => 'json',
            'lat' => $lat,
            'lon' => $lng,
            'addressdetails' => 1,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $suburb = $data['address']['suburb'] ??
                $data['address']['neighbourhood'] ??
                $data['address']['city_district'] ??
                null;

            if ($suburb) {
                $pin->suburb = $suburb;
                $pin->save();
            }
        }
    }
}
