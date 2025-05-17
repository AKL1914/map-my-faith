<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GamePin;
use Illuminate\Http\Request;

class GamePinController extends Controller
{
    public function __construct()
    {
        // Constructor logic if needed
    }
    public function index()
    {
        // Logic to fetch and display game pins
        return view('admin.gamification.gamepins.index');
    }

    public function store(Request $request)
    {
        // Logic to store a new game pin
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'event_id' => 'required|integer',
        ]);

        // Store the game pin in the database
         GamePin::create($validated);

        return response()->json(['message' => 'Game pin created successfully.']);
    }

    public function destroy(GamePin $gamePin, Request $request)
    {
        if($gamePin->event->is_active){
            return response()->json(['message' => 'Event is active. Cannot delete game pin.'], 403);
        }

        $gamePin->delete();

        return response()->json(['message' => 'Game pin deleted successfully.']);
    }



}
