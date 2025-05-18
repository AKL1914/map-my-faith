<?php

namespace App\Http\Controllers;


use App\Models\GamePin;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GamePinController extends Controller
{
    /**
     * Retrieve and return all available game pins associated with active events.
     *
     * This method fetches game pins that are not taken, have no associated user,
     * and belong to events marked as active.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the list of game pins.
     */
    public function index(Request $request)
    {
         $gamePins = GamePin::whereHas('event', function ($query) {
            $query->where('is_active', true);
         })->where('is_taken', false)->where('user_id', null)
            ->get();
        return response()->json($gamePins);
    }

    /**
     * Handle participation in a game pin.
     *
     * This method calculates the distance between the user's location and the game pin's location.
     * If the distance is within the allowed range and the game pin is available, it marks the game pin as taken
     * and records the user's participation.
     *
     * @param \App\Models\GamePin $gamePin The game pin being participated in.
     * @param \Illuminate\Http\Request $request The HTTP request containing user data.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the participation.
     */
    public function participate(GamePin $gamePin, Request $request)
    {
        $userLat = $request->input('latitude');
        $userLng = $request->input('longitude');

        // Commented out for debugging queue issues
        // StoreGamePinParticipant::dispatch(
        //     $gamePin->id,
        //     $request->user()->id,
        //     $userLat,
        //     $userLng
        // );

        // Calculate the distance between the user and the game pin
        $distance = $this->haversineDistance($userLat, $userLng, $gamePin->latitude, $gamePin->longitude);
        if ($distance <= config('app.game_pin_distance')) {
            if (!$gamePin->is_taken && $gamePin->user_id == null) {
                DB::table('game_pins')->where('id', $gamePin->id)->update([
                    'is_taken' => true,
                    'user_id' => $request->user()->id,
                ]);
            }
        }

        // Record the user's participation in the game pin
        DB::table('game_pins_participants')->insert([
            'game_pin_id' => $gamePin->id,
            'user_id' => $request->user()->id,
            'distance' => $distance,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => "Thank you for participating!"
        ], 200);
    }

    /**
     * Calculate the Haversine distance between two geographic coordinates.
     *
     * This method computes the great-circle distance between two points on the Earth's surface
     * using their latitude and longitude.
     *
     * @param float $lat1 Latitude of the first point.
     * @param float $lon1 Longitude of the first point.
     * @param float $lat2 Latitude of the second point.
     * @param float $lon2 Longitude of the second point.
     * @return float The distance in meters between the two points.
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
