<?php

namespace App\Http\Controllers;

use App\Jobs\StoreGamePinParticipant;
use App\Models\GamePin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GamePinController extends Controller
{
    public function index(Request $request)
    {
         $gamePins = GamePin::whereHas('event', function ($query) {
            $query->where('is_active', true);
         })->where('is_taken', false)->where('user_id', null)
            ->get();
        return response()->json($gamePins);
    }

    public function participate(GamePin $gamePin,Request $request)
    {
        $userLat = $request->input('latitude');
        $userLng = $request->input('longitude');

        StoreGamePinParticipant::dispatch(
            $gamePin->id,
            $request->user()->id,
            $userLat,
            $userLng
        );

        //will transfer to queue once inserting is sorted
        $distance = $this->haversineDistance($userLat, $userLng, $gamePin->latitude, $gamePin->longitude);
        if ($distance <= config('app.game_pin_distance')) {
            if (!$gamePin->is_taken && $gamePin->user_id == null) {
                DB::table('game_pins')->where('id', $gamePin->id)->update([
                    'is_taken' => true,
                    'user_id' => $request->user()->id,
                ]);
            }
        }

        return response()->json([
            'success' => "Thank you for participating!"
        ], 200);

    }

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
