<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePinReport;
use App\Models\Pin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
     * @param \Illuminate\Http\Request $request The HTTP request instance.
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
            list($cursor, $keys) = $redis->scan($cursor, ['MATCH' => 'maps_session_*', 'COUNT' => 100]);
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
            'message' => 'Report is being generated and will be emailed to you shortly.'
        ]);
    }

}
