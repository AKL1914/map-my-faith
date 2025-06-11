<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePinReport;
use App\Models\Pin;
use App\Models\Setting;
use Illuminate\Http\Request;
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
        // total number of pins
        $totalPins = \App\Models\Pin::count();
        // total number of users that participated in pins
        $totalParticipants = \App\Models\Pin::distinct('user_id')->count('user_id');
        // get the top 5 pins by number of user the name and number of pins
        $topPins = DB::table('pins')
            ->join('users', 'pins.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.area', DB::raw('count(pins.id) as pinCount'))
            ->where('users.email', '!=', config('app.admin_email'))
            ->where('users.name', '!=', 'Admin') // Exclude user with name 'Admin'
            ->groupBy('users.id', 'users.name', 'users.area')
            ->orderByDesc('pinCount')
            ->limit(5)
            ->get();

//        $query = Pin::with('user');
//
//        $pins = $query->get()
//            ->filter(fn($pin) => isset($pin->user->area));
//
//        $areaCountsRaw = $pins->groupBy(fn($pin) => (int) $pin->user->area)
//            ->map(fn($pins) => $pins->count());
//
//        $areaCounts = $areaCountsRaw->sortKeys()->toArray();
//        $settings = Setting::where('name', 'LIKE', 'AREA_%')
//            ->pluck('value', 'name')
//            ->toArray();


// 1. Get all pins with their users (no date filter, no cache)
        $pins = Pin::with('user')
            ->get()
            ->filter(fn($pin) => isset($pin->user->area));

// 2. Group pins by area and count pins per area
        $areaCountsRaw = $pins->groupBy(fn($pin) => (int) $pin->user->area)
            ->map(fn($pins) => $pins->count());

// 3. Sort by area ID ascending
        $areaCounts = $areaCountsRaw->sortKeys()->toArray();

// 4. Load area values from settings, e.g. 'AREA_1' => 20 (the reference number)
        $settings = Setting::where('name', 'LIKE', 'AREA_%')
            ->pluck('value', 'name')
            ->toArray();

// 5. Build final array with count, setting value, and percentage of count vs setting
        $areasData = [];

        foreach ($areaCounts as $areaId => $count) {
            $key = "AREA_{$areaId}";

            // Get setting value or default 0
            $settingValue = isset($settings[$key]) ? (int) $settings[$key] : 0;

            // Calculate percentage safely
            $percentage = ($settingValue > 0) ? round(($count / $settingValue) * 100, 2) : 0;

            $areasData[$areaId] = [
                'count' => $count,
                'setting_value' => $settingValue,
                'percentage' => $percentage,
            ];
        }

        return view('admin.foyer', [
            'totalPins' => $totalPins,
            'totalParticipants' => $totalParticipants,
            'topPins' => $topPins,
            'areasData' => $areasData,
        ]);
    }
}
