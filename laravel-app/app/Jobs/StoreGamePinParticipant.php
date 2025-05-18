<?php

namespace App\Jobs;

use App\Models\GamePin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StoreGamePinParticipant implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $gamePinId;
    protected $userId;
    protected $latitude;
    protected $longitude;
    protected $distance;

    public function __construct($gamePinId, $userId, $latitude, $longitude)
    {
        $this->gamePinId = $gamePinId;
        $this->userId = $userId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $gamePin = GamePin::find($this->gamePinId);

        $distance = $this->haversineDistance($this->latitude, $this->longitude, $gamePin->latitude, $gamePin->longitude);

        if ($distance <= config('app.game_pin_distance')) {
            if (!$gamePin->is_taken && $gamePin->user_id == null) {
                DB::table('game_pins')->where('id', $gamePin->id)->update([
                    'is_taken' => 1,
                    'user_id' => $this->userId,
                    'distance' => $distance,
                ]);
            }
        }
        DB::table('game_pins_participants')->insert([
            'game_pin_id' => $this->gamePinId,
            'user_id' => $this->userId,
            'distance' => $distance,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

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
