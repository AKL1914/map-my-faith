<?php

namespace App\Http\Controllers;

use App\Models\GamePin;
use Illuminate\Http\Request;

class GamePinController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all game pins
        //game pins is related to event and I just want to get if the event related is_active true
        // and the game pin is not taken
//         $gamePins = GamePin::whereHas('event', function ($query) {
//            $query->where('is_active', true);
//         })->where('is_taken', false)
//            ->get();
        $gamePins = GamePin::where('is_taken', false)->get();

        return response()->json($gamePins);
    }

    public function participate(Request $request)
    {

    }
}
