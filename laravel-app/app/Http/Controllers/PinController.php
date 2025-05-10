<?php

namespace App\Http\Controllers;

use App\Http\Requests\PinStoreRequest;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Models\Pin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PinController extends Controller
{
    public function manage()
    {
        return  view('pins.index');
    }

    public function store(PinStoreRequest $request)
    {

        $pin = Pin::create([
            'user_id' => Auth::id(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'campaign_id' => $request->campaign_id,
            'is_accepted' => $request->is_accepted ?? false,
            'notes' => $request->notes,
        ]);

        return response()->json($pin, 201);
    }

    //get all pins
    public function index(Request $request)
    {
        $perPage = 10;
        $search = $request->query('search');
        $page = $request->query('page', 1);

        // Generate a unique cache key based on search term and page
        $cacheKey = 'pins_' . md5($search . '_page_' . $page . '_per_' . $perPage);

        // Cache the results for 10 minutes
        $pins = Cache::rememberForever($cacheKey, function () use ($search, $perPage) {
            $query = Pin::with([
                'user:id,name,email',
                'campaign:id,name'
            ])->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('notes', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            });

            return $query->paginate($perPage);
        });

        return response()->json($pins);
    }


    //get all pins by campaign
    public function indexByCampaign($campaignId)
    {
        $cacheKey = "pins_campaign_{$campaignId}";

        $pins = Cache::rememberForever($cacheKey, function () use ($campaignId) {
            return Pin::where('campaign_id', $campaignId)->get();
        });

        return response()->json($pins);
    }

    //get all pins by user
    public function indexByUser($userId)
    {
        $cacheKey = "pins_user_{$userId}";

        $pins = Cache::rememberForever($cacheKey, function () use ($userId) {
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

        // Track the cache key
        $allBoundsKeys = Cache::get('pins_bounds_keys', []);
        if (!in_array($cacheKey, $allBoundsKeys)) {
            $allBoundsKeys[] = $cacheKey;
            Cache::forever('pins_bounds_keys', $allBoundsKeys);
        }


        $pins = Cache::rememberForever($cacheKey, function () use ($request) {
            return Pin::with('user:id,name')
                ->where('campaign_id', function ($query) {
                    $query->select('id')
                        ->from('campaigns')
                        ->where('is_active', true);
                })
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

    public function show(Pin $pin)
    {
        return view('pins.show', compact('pin'));
    }

}
