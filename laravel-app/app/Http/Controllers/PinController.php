<?php

namespace App\Http\Controllers;

use App\Http\Requests\PinStoreRequest;
use App\Jobs\FetchSuburbFromCoordinates;
use Illuminate\Http\Request;
use App\Models\Pin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Class PinController
 *
 * This controller handles CRUD operations and data retrieval for the Pin model.
 * It includes methods for managing, storing, retrieving, and deleting pins.
 */
class PinController extends Controller
{
    /**
     * Display the pin management view.
     *
     * @return \Illuminate\View\View The view for managing pins.
     */
    public function manage()
    {
        return view('pins.index');
    }

    /**
     * Store a new pin in the database.
     *
     * This method validates the request data, creates a new pin, and dispatches a job
     * to fetch the suburb from the pin's coordinates.
     *
     * @param \App\Http\Requests\PinStoreRequest $request The validated request containing pin data.
     * @return \Illuminate\Http\JsonResponse A JSON response with the created pin.
     */
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

        FetchSuburbFromCoordinates::dispatch($pin->id);

        return response()->json($pin, 201);
    }

    /**
     * Retrieve and return all pins with optional filters.
     *
     * This method supports filtering by search term, date range, and pagination.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing query parameters.
     * @return \Illuminate\Http\JsonResponse A JSON response with the list of pins.
     */
    public function index(Request $request)
    {
        $perPage = 10;
        $search = $request->query('search');
        $page = $request->query('page', 1);
        $dateFrom = $request->query('from_date');
        $dateTo = $request->query('to_date');

        $query = Pin::with(['user:id,name,email', 'campaign:id,name']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $pins = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json($pins);
    }

    /**
     * Retrieve and return all pins associated with a specific campaign.
     *
     * @param int $campaignId The ID of the campaign.
     * @return \Illuminate\Http\JsonResponse A JSON response with the list of pins.
     */
    public function indexByCampaign($campaignId)
    {
        $cacheKey = "pins_campaign_{$campaignId}";

        $pins = Cache::rememberForever($cacheKey, function () use ($campaignId) {
            return Pin::where('campaign_id', $campaignId)->get();
        });

        return response()->json($pins);
    }

    /**
     * Retrieve and return all pins associated with a specific user.
     *
     * @param int $userId The ID of the user.
     * @return \Illuminate\Http\JsonResponse A JSON response with the list of pins.
     */
    public function indexByUser($userId)
    {
        $cacheKey = "pins_user_{$userId}";

        $pins = Cache::rememberForever($cacheKey, function () use ($userId) {
            return Pin::where('user_id', $userId)->get();
        });

        return response()->json($pins);
    }

    /**
     * Retrieve and return all pins within specified geographic bounds.
     *
     * This method validates the bounds and caches the results for future requests.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing geographic bounds.
     * @return \Illuminate\Http\JsonResponse A JSON response with the list of pins.
     */
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

    /**
     * Delete a specific pin from the database.
     *
     * This method ensures that only the owner of the pin can delete it.
     *
     * @param int $id The ID of the pin to delete.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function destroy($id)
    {
        $pin = Pin::findOrFail($id);

        if ($pin->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pin->delete();

        return response()->json(['message' => 'Deleted']);
    }

    /**
     * Display a specific pin's details.
     *
     * This method loads the associated user and returns the pin details view.
     *
     * @param \App\Models\Pin $pin The pin to display.
     * @return \Illuminate\View\View The view displaying the pin details.
     */
    public function show(Pin $pin)
    {
        $pin->load('user');
        return view('pins.show', compact('pin'));
    }
}
