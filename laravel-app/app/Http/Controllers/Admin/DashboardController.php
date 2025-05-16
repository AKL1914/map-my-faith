<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.dashboard');
    }

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
}
