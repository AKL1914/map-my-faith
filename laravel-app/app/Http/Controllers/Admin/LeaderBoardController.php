<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LeaderBoardController extends Controller
{
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

        // NEW: Top 10 areas based on pin counts (grouped by user's area)
        $topAreas = DB::table('pins')
            ->join('users', 'pins.user_id', '=', 'users.id')
            ->select('users.area', DB::raw('count(pins.id) as pinCount'))
            ->groupBy('users.area')
            ->orderByDesc('pinCount')
            ->limit(10)
            ->get();

        return view('admin.leaderboard.index', compact('leaderboardData', 'topSuburbs', 'topAreas'));
    }

    public function show($id)
    {
        // Fetch specific leaderboard entry by ID
        $entry = []; // Replace with actual data fetching logic

        return view('admin.leaderboard.show', compact('entry'));
    }
}
