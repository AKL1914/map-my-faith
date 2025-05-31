<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderBoardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user() && $request->user()->is_admin) {
            // Admin user — do not cache
            $leaderboardData = $this->getLeaderboardData();
            $topSuburbs = $this->getTopSuburbs();
            $topAreas = $this->getTopAreas();
        } else {
            // Non-admin user — cache for 1 hour
            $leaderboardData = cache()->remember('leaderboardData', 3600, function () {
                return $this->getLeaderboardData();
            });

            $topSuburbs = cache()->remember('topSuburbs', 3600, function () {
                return $this->getTopSuburbs();
            });

            $topAreas = cache()->remember('topAreas', 3600, function () {
                return $this->getTopAreas();
            });
        }

        return view('admin.leaderboard.index', compact('leaderboardData', 'topSuburbs', 'topAreas'));
    }

    private function getLeaderboardData()
    {
        return DB::table('pins')
            ->join('users', 'pins.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.area', DB::raw('count(pins.id) as pinCount'))
            ->where('users.email', '!=', config('app.admin_email')) // Exclude admin
            ->groupBy('users.id', 'users.name', 'users.area')
            ->orderByDesc('pinCount')
            ->limit(10)
            ->get();
    }

    private function getTopSuburbs()
    {
        return DB::table(DB::raw('
        (
            SELECT
                pins.suburb,
                COUNT(pins.id) AS totalPins
            FROM pins
            GROUP BY pins.suburb
        ) AS total
    '))
            ->join(DB::raw('
        (
            SELECT
                pins.suburb,
                users.name AS user_name,
                COUNT(pins.id) AS userPins,
                ROW_NUMBER() OVER (PARTITION BY pins.suburb ORDER BY COUNT(pins.id) DESC) AS row_num
            FROM pins
            JOIN users ON pins.user_id = users.id
            GROUP BY pins.suburb, users.name
        ) AS topuser
    '), 'total.suburb', '=', 'topuser.suburb')
            ->where('topuser.row_num', 1)
            ->orderByDesc('total.totalPins')
            ->limit(10)
            ->select('total.suburb', 'total.totalPins', 'topuser.user_name', 'topuser.userPins')
            ->get();
    }

    private function getTopAreas()
    {
        return DB::table('pins')
            ->join('users', 'pins.user_id', '=', 'users.id')
            ->select('users.area', DB::raw('count(pins.id) as pinCount'))
            ->groupBy('users.area')
            ->orderByDesc('pinCount')
            ->limit(10)
            ->get();
    }


    public function show($id)
    {
        $entry = []; // Placeholder for show logic
        return view('admin.leaderboard.show', compact('entry'));
    }
}
