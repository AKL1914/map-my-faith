<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Models\Pin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PinController extends Controller
{

    public function store(Request $request)
    {
        $activeCampaign = Campaign::where('is_active', true)->first();
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $pin = Pin::create([
            'user_id' => Auth::id(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'campaign_id' => $activeCampaign->id,
            'is_accepted' => $request->is_accepted ?? false,
            'notes' => $request->notes,
        ]);

        return response()->json($pin, 201);
    }

    //get all pins
    public function index()
    {

        $pins = Pin::with('user:id,name')->get(); // Load only the user ID and name
        return response()->json($pins);

    }

    //get all pins by campaign
    public function indexByCampaign($campaignId)
    {
        $cacheKey = "pins_campaign_{$campaignId}";

        $pins = Cache::remember($cacheKey, 60, function () use ($campaignId) {
            return Pin::where('campaign_id', $campaignId)->get();
        });

        return response()->json($pins);
    }

    //get all pins by user
    public function indexByUser($userId)
    {
        $cacheKey = "pins_user_{$userId}";

        $pins = Cache::remember($cacheKey, 60, function () use ($userId) {
            return Pin::where('user_id', $userId)->get();
        });

        return response()->json($pins);
    }
    public function indexByBounds(Request $request)
    {
        $request->validate([
            'north' => 'required|numeric',
            'south' => 'required|numeric',
            'east' => 'required|numeric',
            'west' => 'required|numeric',
        ]);

        $cacheKey = 'pins_bounds_' . md5(json_encode([
                $request->north,
                $request->south,
                $request->east,
                $request->west,
            ]));


        $pins = Cache::remember($cacheKey, 10, function () use ($request) {
            return Pin::with('user:id,name')
                ->whereBetween('latitude', [$request->south, $request->north])
                ->whereBetween('longitude', [$request->west, $request->east])
                ->get();
        });

        return response()->json($pins);
    }

    public function destroy($id)
    {
        $pin = Pin::findOrFail($id);

        if ($pin->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pin->delete();

        return response()->json(['message' => 'Deleted']);
    }


}
