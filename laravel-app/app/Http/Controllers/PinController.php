<?php

namespace App\Http\Controllers;

use App\Http\Requests\PinStoreRequest;
use App\Jobs\FetchSuburbFromCoordinates;
use App\Models\Pin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
     * Contact fields captured at a pin's location. Admin-facing and report
     * endpoints only — never returned to member-facing pin listings.
     */
    private const CONTACT_FIELDS = ['contact_name', 'contact_phone', 'contact_email'];

    /**
     * Cache key holding the current bounds-cache "generation". Bumping it
     * invalidates every previously cached bounds result without having to
     * enumerate or delete individual keys.
     */
    public const BOUNDS_CACHE_VERSION_KEY = 'pins_bounds_version';

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
     * @param  \App\Http\Requests\PinStoreRequest  $request  The validated request containing pin data.
     * @return \Illuminate\Http\JsonResponse A JSON response with the created pin.
     */
    public function store(PinStoreRequest $request)
    {
        // Define proximity threshold (approximately 1 meter in degrees)
        $proximityThreshold = 0.00001; // Roughly 1 meter
        
        // Check if a pin within the proximity threshold already exists for this user and campaign
        $existingPin = Pin::where('user_id', Auth::id())
            ->where('campaign_id', $request->campaign_id)
            ->whereBetween('latitude', [
                $request->latitude - $proximityThreshold, 
                $request->latitude + $proximityThreshold
            ])
            ->whereBetween('longitude', [
                $request->longitude - $proximityThreshold, 
                $request->longitude + $proximityThreshold
            ])
            ->first();

        if ($existingPin) {
            return response()->json([
                'message' => 'Cannot Pin on the same location'
            ], 422);
        }

        $pin = Pin::create([
            'user_id' => Auth::id(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'campaign_id' => $request->campaign_id,
            'is_accepted' => $request->is_accepted ?? false,
            'notes' => $request->notes,
            'contact_name' => $request->contact_name,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
        ]);

        FetchSuburbFromCoordinates::dispatch($pin->id);

        return response()->json($pin, 201);
    }

    /**
     * Retrieve and return all pins with optional filters.
     *
     * This method supports filtering by search term, date range, and pagination.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing query parameters.
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
     * @param  int  $campaignId  The ID of the campaign.
     * @return \Illuminate\Http\JsonResponse A JSON response with the list of pins.
     */
    public function indexByCampaign($campaignId, Request $request)
    {
        $cacheKey = "pins_campaign_{$campaignId}";

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $area = $request->query('area');
        $limit = $request->query('limit');

        $pinFields = ['id', 'user_id', 'latitude', 'longitude', 'notes', 'is_accepted', 'suburb', 'created_at', 'campaign_id'];

        $useFilters = $dateFrom || $dateTo || $area;

        $query = Pin::select($pinFields)
            ->with(['user:id,name,area'])
            ->where('campaign_id', $campaignId);

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($area) {
            $query->whereHas('user', function ($q) use ($area) {
                $q->where('area', $area);
            });
        }

        if ($useFilters) {
            $pins = $query->get();
        } else {
            $pins = Cache::rememberForever($cacheKey, function () use ($campaignId, $pinFields) {
                return Pin::select($pinFields)
                    ->with(['user:id,name,area'])
                    ->where('campaign_id', $campaignId)
                    ->get();
            });
        }

        $totalPins = $pins->count();
        $totalUsers = $pins->pluck('user_id')->unique()->count();

        // Only limit displayed data — totals remain full
        if (! $useFilters && is_numeric($limit)) {
            $pins = $pins->take((int) $limit)->values();
        }

        return response()->json([
            'data' => $pins,
            'total_pins' => $totalPins,
            'total_users' => $totalUsers,
        ]);
    }

    /**
     * Retrieve and return all pins associated with a specific user.
     *
     * @param  int  $userId  The ID of the user.
     * @return \Illuminate\Http\JsonResponse A JSON response with the list of pins.
     */
    public function indexByUser($userId)
    {
        $cacheKey = "pins_user_{$userId}";

        $pins = Cache::rememberForever($cacheKey, function () use ($userId) {
            return Pin::where('user_id', $userId)->get();
        });

        return response()->json($pins->makeHidden(self::CONTACT_FIELDS));
    }

    /**
     * Retrieve and return all pins within specified geographic bounds.
     *
     * This method validates the bounds and caches the results for future requests.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing geographic bounds.
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

        // Snap the viewport out to a coarse grid (3dp ≈ 111m) so nearby pans
        // share a cache entry instead of every pixel-level move missing the
        // cache. Always expand outward (floor/ceil), never shrink, so the
        // cached box fully covers what was actually requested.
        $scale = 1000;
        $north = ceil($request->north * $scale) / $scale;
        $south = floor($request->south * $scale) / $scale;
        $east = ceil($request->east * $scale) / $scale;
        $west = floor($request->west * $scale) / $scale;

        // A single version counter replaces the old per-key registry: pin and
        // campaign writes bump this instead of enumerating and deleting every
        // bounds key ever cached. Stale entries just expire via TTL.
        // Default to 0 (not 1) so the very first pin/campaign write — which
        // takes the counter from unset to 1 via Cache::increment() — is
        // guaranteed to differ from whatever was read before it existed.
        $version = Cache::get(self::BOUNDS_CACHE_VERSION_KEY, 0);

        $cacheKey = sprintf('pins_bounds_v%d_%s', $version, md5(json_encode([$north, $south, $east, $west])));

        $pins = Cache::remember($cacheKey, now()->addMinutes(2), function () use ($north, $south, $east, $west) {
            return Pin::with('user:id,name')
                ->where('campaign_id', function ($query) {
                    $query->select('id')
                        ->from('campaigns')
                        ->where('is_active', true);
                })
                ->whereBetween('latitude', [$south, $north])
                ->whereBetween('longitude', [$west, $east])
                ->get();
        });

        return response()->json($pins->makeHidden(self::CONTACT_FIELDS));
    }

    /**
     * Delete a specific pin from the database.
     *
     * This method ensures that only the owner of the pin can delete it.
     *
     * @param  int  $id  The ID of the pin to delete.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function destroy($id)
    {
        $pin = Pin::findOrFail($id);

        // if user is admin, allow deletion of any pin
        if ($pin->user_id !== auth()->id() && ! auth()->user()->is_admin) {
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
     * @param  \App\Models\Pin  $pin  The pin to display.
     * @return \Illuminate\View\View The view displaying the pin details.
     */
    public function show(Pin $pin)
    {
        $pin->load('user');

        return view('pins.show', compact('pin'));
    }

    public function pinsByUser(User $user, Request $request)
    {
        if (Auth::id() !== $user->id && ! Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cacheKey = "user_pins_{$user->id}";

        $userPins = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
            return $user->pins()->get();
        });

        return response()->json([
            'pins' => $userPins->makeHidden(self::CONTACT_FIELDS),
            'user_name' => $user->name,
            'total_pins' => $userPins->count(),
        ]);
    }

    public function update(Pin $pin, Request $request)
    {
        $pin->update($request->only(['is_accepted']));

        return response()->json(['message' => 'Pin updated successfully']);
    }
    public function pinsByArea(Request $request)
    {
        $days = (int) $request->input('days', 7);

        $cacheKey = "pins_by_area_{$days}";
        $cacheDuration = now()->addHour(); // 1 hour

        return Cache::remember($cacheKey, $cacheDuration, function () use ($days) {
            $startDate = Carbon::now()->subDays($days - 1)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            $pins = Pin::with('user')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->groupBy(fn ($pin) => $pin->created_at->format('Y-m-d'))
                ->map(
                    fn ($pinsOnDay) => $pinsOnDay
                    ->filter(fn ($pin) => isset($pin->user->area) && in_array($pin->user->area, range(1, 6)))
                    ->groupBy(fn ($pin) => (int)$pin->user->area)
                    ->map->count()
                );

            $labels = [];
            $areas = [];

            foreach (range(1, 6) as $areaId) {
                $areas[$areaId] = array_fill(0, $days, 0);
            }

            foreach (range(0, $days - 1) as $i) {
                $date = Carbon::now()->subDays($days - 1 - $i)->format('Y-m-d');
                $labels[] = $date;

                if (isset($pins[$date])) {
                    foreach ($pins[$date] as $areaId => $count) {
                        $areaId = (int) $areaId;
                        if (isset($areas[$areaId])) {
                            $areas[$areaId][$i] = $count;
                        }
                    }
                }
            }

            return [
                'labels' => $labels,
                'areas' => $areas,
            ];
        });
    }

    public function pinsDistributionByArea(Request $request)
    {
        $days = $request->query('days');
        $cacheKey = 'pins_distribution_by_area_' . ($days ?? 'all');

        $areaCounts = Cache::remember($cacheKey, 3600, function () use ($days) {
            $query = Pin::with('user');

            if (in_array($days, [7, 15, 30])) {
                $fromDate = Carbon::now()->subDays($days);
                $query->where('created_at', '>=', $fromDate);
            }

            $pins = $query->get()
                ->filter(fn ($pin) => isset($pin->user->area) && in_array($pin->user->area, range(1, 6)));

            $areaCountsRaw = $pins->groupBy(fn ($pin) => (int) $pin->user->area)
                ->map(fn ($pins) => $pins->count());

            $areaCounts = [];
            foreach (range(1, 6) as $areaId) {
                $areaCounts[$areaId] = $areaCountsRaw->get($areaId, 0);
            }

            return $areaCounts;
        });

        return response()->json([
            'area_counts' => $areaCounts,
        ]);
    }




}
