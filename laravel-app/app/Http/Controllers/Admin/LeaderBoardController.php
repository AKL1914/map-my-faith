<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Class LeaderBoardController
 *
 * This controller handles the display and management of leaderboard data in the admin panel.
 * It provides methods to retrieve top users, suburbs, and areas based on pin counts.
 */
class LeaderBoardController extends Controller
{
    /**
     * Display the leaderboard view.
     *
     * This method retrieves and prepares data for the leaderboard, including:
     * - Top 10 users based on pin count.
     * - Top 10 suburbs based on pin count.
     * - Top 10 areas based on pin count (grouped by user's area).
     *
     * @return \Illuminate\View\View The leaderboard view with the prepared data.
     */
    public function index()
    {
        // Top 10 users based on pin count
        $leaderboardData = DB::table('pins')
            ->join('users', 'pins.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.area', DB::raw('count(pins.id) as pinCount'))
            ->where('users.email', '!=', config('app.admin_email')) // Exclude admin
            ->groupBy('users.id', 'users.name', 'users.area')
            ->orderByDesc('pinCount')
            ->limit(10)
            ->get();

        // Top 10 suburbs
        $topSuburbs = DB::table('pins')
            ->select('suburb', DB::raw('count(*) as pinCount'))
            ->groupBy('suburb')
            ->orderByDesc('pinCount')
            ->limit(10)
            ->get();

        // Top 10 areas based on pin counts (grouped by user's area)
        $topAreas = DB::table('pins')
            ->join('users', 'pins.user_id', '=', 'users.id')
            ->select('users.area', DB::raw('count(pins.id) as pinCount'))
            ->groupBy('users.area')
            ->orderByDesc('pinCount')
            ->limit(10)
            ->get();

        return view('admin.leaderboard.index', compact('leaderboardData', 'topSuburbs', 'topAreas'));
    }

    /**
     * Display a specific leaderboard entry.
     *
     * This method fetches and displays details for a specific leaderboard entry by its ID.
     *
     * @param int $id The ID of the leaderboard entry to display.
     * @return \Illuminate\View\View The view displaying the leaderboard entry details.
     */
    public function show($id)
    {
        // Fetch specific leaderboard entry by ID
        $entry = []; // Replace with actual data fetching logic

        return view('admin.leaderboard.show', compact('entry'));
    }
}
