<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePinReport;
use App\Models\Pin;
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

        // Use SCAN to iterate over keys with your session prefix
        do {
            // SCAN returns an array with [cursor, keys]
            [$cursor, $keys] = $redis->scan($cursor, ['MATCH' => 'maps_session_*', 'COUNT' => 100]);
            if ($keys) {
                $count += count($keys);
            }
        } while ($cursor != 0);

        return response()->json([
            'count' => $count,
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

        // Cache area distribution data for 1 hour
        $areasData = Cache::remember('foyer_areas_data', 3600, function () {
            $pins = Pin::with('user')
                ->get()
                ->filter(fn ($pin) => isset($pin->user->area));

            $areaCountsRaw = $pins->groupBy(fn ($pin) => (int) $pin->user->area)
                ->map(fn ($pins) => $pins->count());

            $areaCounts = $areaCountsRaw->sortKeys()->toArray();

            $settings = \App\Models\Setting::where('name', 'LIKE', 'AREA_%')
                ->pluck('value', 'name')
                ->toArray();

            $result = [];
            foreach ($areaCounts as $areaId => $count) {
                $key = "AREA_{$areaId}";
                $settingValue = isset($settings[$key]) ? (int) $settings[$key] : 0;
                $percentage = ($settingValue > 0) ? round(($count / $settingValue) * 100, 2) : 0;

                $result[$areaId] = [
                    'count' => $count,
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
