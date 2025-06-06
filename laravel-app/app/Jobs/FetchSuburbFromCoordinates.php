<?php

namespace App\Jobs;

use App\Models\Pin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

/**
 * Class FetchSuburbFromCoordinates
 *
 * This job is responsible for fetching the suburb information for a given pin
 * based on its latitude and longitude using the Nominatim OpenStreetMap API.
 */
class FetchSuburbFromCoordinates implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The ID of the pin for which the suburb information is to be fetched.
     *
     * @var int
     */
    protected $pinId;

    /**
     * Create a new job instance.
     *
     * @param  int  $pinId  The ID of the pin.
     */
    public function __construct($pinId)
    {
        $this->pinId = $pinId;
    }

    /**
     * Execute the job.
     *
     * This method retrieves the pin by its ID, fetches the suburb information
     * from the Nominatim OpenStreetMap API, and updates the pin's suburb field
     * if the information is successfully retrieved.
     */
    public function handle(): void
    {
        // Retrieve the pin by its ID
        $pin = Pin::find($this->pinId);

        // If the pin does not exist, exit the job
        if (! $pin) {
            return;
        }

        // Extract latitude and longitude from the pin
        $lat = $pin->latitude;
        $lng = $pin->longitude;

        // Make a request to the Nominatim OpenStreetMap API
        $response = Http::withHeaders([
            'User-Agent' => 'maps/1.0 (villamornatonio@gmail.com)', // Set the correct User-Agent header
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'format' => 'json',
            'lat' => $lat,
            'lon' => $lng,
            'addressdetails' => 1,
        ]);

        // If the API response is successful, process the data
        if ($response->successful()) {
            $data = $response->json();

            // Extract the suburb or fallback to other address components
            $suburb = $data['address']['suburb'] ??
                $data['address']['neighbourhood'] ??
                $data['address']['city_district'] ??
                null;

            // If a suburb is found, update the pin's suburb field
            if ($suburb) {
                $pin->suburb = $suburb;
                $pin->save();
            }
        }
    }
}
