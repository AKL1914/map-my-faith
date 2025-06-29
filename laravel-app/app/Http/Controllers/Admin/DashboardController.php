<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePinReport;
use App\Models\Pin;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

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
        // Use Redis connection for sessions
        $redis = Redis::connection('session');

        $count = 0;
        $cursor = null;
        $userNames = [];
        $sessionKeys = [];

        // Use SCAN to iterate over keys with your session prefix
        do {
            // SCAN returns an array with [cursor, keys]
            [$cursor, $keys] = $redis->scan($cursor, ['MATCH' => 'maps_session_*', 'COUNT' => 100]);
            if ($keys) {
                $count += count($keys);
                $sessionKeys = array_merge($sessionKeys, $keys);
            }
        } while ($cursor != 0);

        // Get user names from session data
        foreach ($sessionKeys as $key) {
            $session = $redis->get($key);
            if ($session) {
                $data = @unserialize($session);
                $foundName = null;
                if (is_array($data) && isset($data['login_web_' . auth()->getDefaultDriver()])) {
                    $userArray = $data['login_web_' . auth()->getDefaultDriver()];
                    if (is_array($userArray) && isset($userArray['name'])) {
                        $foundName = $userArray['name'];
                    }
                } elseif (is_array($data) && isset($data['user'])) {
                    $user = $data['user'];
                    if (is_array($user) && isset($user['name'])) {
                        $foundName = $user['name'];
                    }
                } else {
                    // Try JSON decode
                    $json = @json_decode($session, true);
                    if (is_array($json)) {
                        if (isset($json['user']['name'])) {
                            $foundName = $json['user']['name'];
                        } elseif (isset($json['name'])) {
                            $foundName = $json['name'];
                        }
                    }
                }
                // Fallback: regex search for "name"
                if (!$foundName && is_string($session)) {
                    if (preg_match('/"name";s:\d+:"([^"]+)"/', $session, $matches)) {
                        $foundName = $matches[1];
                    } elseif (preg_match('/"name":"([^"]+)"/', $session, $matches)) {
                        $foundName = $matches[1];
                    }
                }
                if ($foundName) {
                    $userNames[] = $foundName;
                }
            }
        }

        // Remove duplicates and get top 3 names (by order of appearance, allow duplicates)
        $top3Names = array_slice($userNames, 0, 3);

        return response()->json([
            'count' => $count,
            'top3' => $top3Names,
        ]);
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
                ->select('users.id', 'users.name', 'users.area', DB::raw('count(pins.id) as pinCount'))
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
