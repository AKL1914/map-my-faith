<?php
namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Models\Pin;
use Illuminate\Support\Facades\Auth;

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
        $pins = Pin::all();
        return response()->json($pins);

    }

    //get all pins by campaign
    public function indexByCampaign($campaignId)
    {
        $pins = Pin::where('campaign_id', $campaignId)->get();
        return response()->json($pins);
    }

    //get all pins by user
    public function indexByUser($userId)
    {
        $pins = Pin::where('user_id', $userId)->get();
        return response()->json($pins);
    }


}
