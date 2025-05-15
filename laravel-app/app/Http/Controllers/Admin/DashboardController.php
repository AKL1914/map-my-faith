<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pin;
use App\Models\Campaign;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.dashboard');
    }

    public function getActiveUsersCount()
    {
//        $dbIndex = config('database.redis.default.database');
//        dd('Redis DB index:', $dbIndex);
//        // 3. List **all** keys in the Redis DB (limit output to 20 for sanity):
//        $allKeys = Redis::keys('*');
//        dd('All Redis keys:', array_slice($allKeys, 0, 20));
        $prefix = config('database.redis.default.database');
        $pattern = $prefix . ':*';

        // Get all session keys (this can be heavy on large Redis)
        $keys = Redis::keys($pattern);

        $userIds = [];

        foreach ($keys as $key) {
            $payload = Redis::get($key);
            if (!$payload) {
                continue;
            }

            // Laravel session payload format:
            // Sometimes encrypted; if you don't encrypt sessions, payload is plain PHP serialized string.
            // If encrypted, you need to decrypt it using Laravel encryption.
            // Assuming sessions are NOT encrypted for simplicity here.

            // Unserialize the payload
            $data = @unserialize($payload);

            if ($data === false && $payload !== serialize(false)) {
                // Payload may be encrypted or not unserializable, skip
                continue;
            }

            // The user ID is usually under '_login_web_' or similar keys in the session array
            // This depends on your auth guard. Let's try to get 'login_web_123' key:
            // Instead, it's safer to check for 'login_web_*' or 'login_web' keys.

            foreach ($data as $keyData => $value) {
                if (Str::startsWith($keyData, 'login_web')) {
                    // $value is user id normally
                    if (is_numeric($value)) {
                        $userIds[] = $value;
                    }
                }
            }

            // Alternative approach: if you know exact session key user_id is stored in, check that
        }

        $uniqueUsers = count(array_unique($userIds));

        return response()->json([
            'count' => $uniqueUsers,
        ]);
    }
}
