<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePinReport;
use App\Models\Pin;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

/**
 * Class DashboardController
 *
 * This controller handles the display of the admin dashboard and provides
 * functionality to retrieve active user counts using Redis.
 */
class DashboardController extends Controller
{
    /**
     * Display the admin dashboard view.
     *
     * This method returns the main dashboard page for the admin panel.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request instance.
     * @return \Illuminate\View\View The admin dashboard view.
     */
    public function index(Request $request)
    {
        return view('admin.dashboard');
    }

    /**
     * Retrieve the count of active users.
     *
     * This method uses Redis to scan session keys and calculate the number
     * of active users based on the session data.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the count of active users.
     */
    public function getActiveUsersCount()
    {
        $activeUserIds = config('session.driver') === 'database'
            ? DB::table(config('session.table', 'sessions'))
                ->whereNotNull('user_id')
                ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime'))->timestamp)
                ->distinct()
                ->pluck('user_id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all()
            : $this->activeUserIdsFromRedis();

        $users = User::query()
            ->whereIn('id', $activeUserIds)
            ->get(['id', 'name'])
            ->keyBy('id');

        $top3Names = collect($activeUserIds)
            ->map(fn ($id) => $users->get($id)?->name)
            ->filter()
            ->unique()
            ->take(3)
            ->values()
            ->all();

        return response()->json([
            'count' => count($activeUserIds),
            'top3' => $top3Names,
        ]);
    }

    /**
     * Return the latest known pin location for each currently active user.
     */
    public function getActiveUsersLocations()
    {
        $activeUserIds = config('session.driver') === 'database'
            ? DB::table(config('session.table', 'sessions'))
                ->whereNotNull('user_id')
                ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime'))->timestamp)
                ->distinct()
                ->pluck('user_id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all()
            : $this->activeUserIdsFromRedis();

        $locations = Pin::query()
            ->with('user:id,name')
            ->whereIn('user_id', $activeUserIds)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()
            ->get(['user_id', 'latitude', 'longitude', 'created_at'])
            ->unique('user_id')
            ->values()
            ->map(fn (Pin $pin) => [
                'user_id' => $pin->user_id,
                'name' => $pin->user?->name ?? 'Unknown User',
                'latitude' => (float) $pin->latitude,
                'longitude' => (float) $pin->longitude,
                'updated_at' => $pin->created_at?->toIso8601String(),
            ]);

        return response()->json([
            'locations' => $locations,
        ]);
    }

    /**
     * Read authenticated user IDs from the Redis-backed session store.
     *
     * Redis SCAN returns keys with the connection prefix already applied, so
     * that prefix must be removed before reading each key again.
     *
     * @return array<int, int>
     */
    private function activeUserIdsFromRedis(): array
    {
        $redis = Redis::connection('session');
        $connectionPrefix = (string) config('database.redis.session.prefix', '');
        $cachePrefix = (string) config('cache.prefix', '');
        $cursor = null;
        $userIds = [];
        $loginPrefix = 'login_'.auth()->getDefaultDriver().'_';

        do {
            [$cursor, $keys] = $redis->scan($cursor, [
                'MATCH' => $cachePrefix.'*',
                'COUNT' => 100,
            ]);

            foreach ($keys ?: [] as $key) {
                $redisKey = Str::startsWith($key, $connectionPrefix)
                    ? Str::after($key, $connectionPrefix)
                    : $key;
                $rawSession = $redis->get($redisKey);
                $payload = @unserialize($rawSession);
                $payload = is_string($payload) ? @unserialize($payload) : $payload;

                if (!is_array($payload)) {
                    continue;
                }

                foreach ($payload as $sessionKey => $value) {
                    if (Str::startsWith((string) $sessionKey, $loginPrefix) && is_scalar($value)) {
                        $userIds[(int) $value] = (int) $value;
                    }
                }
            }
        } while ((int) $cursor !== 0);

        return array_values(array_filter($userIds, fn (int $id) => $id > 0));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'campaign_id' => 'nullable|integer|exists:campaigns,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $filters = $request->only('campaign_id', 'start_date', 'end_date');
        $email = auth()->user()->email;

        GeneratePinReport::dispatch($filters, $email);

        return response()->json([
            'message' => 'Report is being generated and will be emailed to you shortly.',
        ]);
    }


    public function foyer(Request $request)
    {
        // check if the user is authenticated and has admin privileges
        if (!auth()->check()) {
            $key = $request->query('key');
            $showFoyer = Setting::where('name', 'SHOW_FOYER')->where('enabled', 1)->value('value');

            if ($key !== $showFoyer || !$showFoyer) {
                abort(404, 'Foyer is not available!');
            }
        }

        // Cache total number of pins for 1 hour
        $totalPins = Cache::remember('foyer_total_pins', 3600, function () {
            return Pin::count();
        });

        // Cache total number of participants for 1 hour
        $totalParticipants = Cache::remember('foyer_total_participants', 3600, function () {
            return Pin::distinct('user_id')->count('user_id');
        });

        // Cache top 5 pin users for 1 hour
        $topPins = Cache::remember('foyer_top_pins', 3600, function () {
            return DB::table('pins')
                ->join('users', 'pins.user_id', '=', 'users.id')
                ->select('users.id', 'users.name', 'users.area', DB::raw('count(pins.id) as "pinCount"'))
                ->where('users.email', '!=', config('app.admin_email'))
                ->where('users.name', '!=', 'Admin')
                ->groupBy('users.id', 'users.name', 'users.area')
                ->orderByDesc('pinCount')
                ->limit(5)
                ->get();
        });

        $areasData = Cache::remember('foyer_areas_data', 3600, function () {
            // Get users with an area
            $users = \App\Models\User::whereNotNull('area')->get();

            // Count users per area
            $userCountsRaw = $users->groupBy(fn ($user) => (int) $user->area)
                ->map(fn ($users) => $users->count());

            $userCounts = $userCountsRaw->sortKeys()->toArray();

            // Get pins with users who have areas
            $pins = \App\Models\Pin::with('user')
                ->get()
                ->filter(fn ($pin) => isset($pin->user?->area));

            // Count pins per area (based on the user's area)
            $pinCountsRaw = $pins->groupBy(fn ($pin) => (int) $pin->user->area)
                ->map(fn ($pins) => $pins->count());

            $pinCounts = $pinCountsRaw->sortKeys()->toArray();

            // Get area setting values (targets)
            $settings = \App\Models\Setting::where('name', 'LIKE', 'AREA_%')
                ->pluck('value', 'name')
                ->toArray();

            // Build the final result per area
            $allAreaIds = array_unique(array_merge(array_keys($userCounts), array_keys($pinCounts)));

            $result = [];
            foreach ($allAreaIds as $areaId) {
                $userCount = $userCounts[$areaId] ?? 0;
                $pinCount = $pinCounts[$areaId] ?? 0;
                $key = "AREA_{$areaId}";
                $settingValue = isset($settings[$key]) ? (int) $settings[$key] : 0;
                $percentage = ($settingValue > 0) ? round(($userCount / $settingValue) * 100) : 0;

                $result[$areaId] = [
                    'user_count' => $userCount,
                    'pin_count' => $pinCount,
                    'setting_value' => $settingValue,
                    'percentage' => $percentage,
                ];
            }

            return $result;
        });

        return view('admin.foyer', [
            'totalPins' => $totalPins,
            'totalParticipants' => $totalParticipants,
            'topPins' => $topPins,
            'areasData' => $areasData,
        ]);
    }
}
