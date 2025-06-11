<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class LeaderBoardController
 *
 * Handles the leaderboard functionality for the admin panel, including fetching
 * leaderboard data, top suburbs, and top areas based on pins created by users.
 */
class LeaderBoardController extends Controller
{
    /**
     * Display the leaderboard page.
     *
     * Fetches leaderboard data, top suburbs, and top areas either from the cache
     * or directly from the database, depending on whether the user is an admin.
     *
     * @param Request $request The incoming HTTP request.
     * @return \Illuminate\View\View The leaderboard view with the required data.
     */
    public function index(Request $request)
    {
        if ($request->user() && $request->user()->is_admin) {
            $leaderboardData = $this->getLeaderboardData();
            $topSuburbs = $this->getTopSuburbs();
            $topAreas = $this->getTopAreas();
        } else {
            $leaderboardData = cache()->remember('leaderboardData', now()->addMinutes(30), function () {
                return $this->getLeaderboardData();
            });

            $topSuburbs = cache()->remember('topSuburbs', now()->addMinutes(30), function () {
                return $this->getTopSuburbs();
            });

            $topAreas = cache()->remember('topAreas', now()->addMinutes(30), function () {
                return $this->getTopAreas();
            });
        }

        return view('admin.leaderboard.index', compact('leaderboardData', 'topSuburbs', 'topAreas'));
    }

    /**
     * Fetch leaderboard data.
     *
     * Retrieves the top 10 users based on the number of pins they have created,
     * excluding the admin user.
     *
     * @return \Illuminate\Support\Collection The leaderboard data.
     */
    private function getLeaderboardData()
    {
        return DB::table('pins')
            ->join('users', 'pins.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.area', DB::raw('count(pins.id) as pinCount'))
            ->where('users.email', '!=', config('app.admin_email'))
            ->where('users.name', '!=', 'Admin') // Exclude user with name 'Admin'
            ->groupBy('users.id', 'users.name', 'users.area')
            ->orderByDesc('pinCount')
            ->limit(10)
            ->get();
    }

    /**
     * Fetch top suburbs.
     *
     * Retrieves the top 10 suburbs based on the total number of pins created,
     * along with the user who created the most pins in each suburb.
     *
     * @return \Illuminate\Support\Collection The top suburbs data.
     */
    private function getTopSuburbs()
    {
        return DB::table(DB::raw('
                (
                    SELECT
                        pins.suburb,
                        COUNT(pins.id) AS totalPins
                    FROM pins
                    JOIN users ON pins.user_id = users.id
                    WHERE users.name != "Admin"
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
                    WHERE users.name != "Admin"
                    GROUP BY pins.suburb, users.name
                ) AS topuser
            '), 'total.suburb', '=', 'topuser.suburb')
            ->where('topuser.row_num', 1)
            ->orderByDesc('total.totalPins')
            ->limit(10)
            ->select('total.suburb', 'total.totalPins', 'topuser.user_name', 'topuser.userPins')
            ->get();
    }

    /**
     * Fetch top areas.
     *
     * Retrieves the top 10 areas based on the total number of pins created,
     * excluding the admin user.
     *
     * @return \Illuminate\Support\Collection The top areas data.
     */
    private function getTopAreas()
    {
        return DB::table('pins')
            ->join('users', 'pins.user_id', '=', 'users.id')
            ->where('users.email', '!=', config('app.admin_email'))
            ->select('users.area', DB::raw('count(pins.id) as pinCount'))
            ->groupBy('users.area')
            ->orderByDesc('pinCount')
            ->limit(10)
            ->get();
    }

    /**
     * Display a specific leaderboard entry.
     *
     * Currently a placeholder method for showing detailed leaderboard entry data.
     *
     * @param int $id The ID of the leaderboard entry.
     * @return \Illuminate\View\View The view for the leaderboard entry.
     */
    public function show($id)
    {
        $entry = []; // Placeholder

        return view('admin.leaderboard.show', compact('entry'));
    }
}
