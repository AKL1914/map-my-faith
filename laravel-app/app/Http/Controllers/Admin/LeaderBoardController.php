<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\FetchSuburbFromCoordinates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderBoardController extends Controller
{
    public function index()
    {
        // Uncomment the following line to dispatch the job for a specific pin
        // This is just an example and should be removed in production
        // Make sure to replace 1 with the actual pin ID you want to process
//        $pinId = 1;
//        FetchSuburbFromCoordinates::dispatchSync($pinId);


        // Top 10 users based on pin count
        $leaderboardData = DB::table('pins')
            ->join('users', 'pins.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('count(pins.id) as pinCount'))
            ->groupBy('users.id')
            ->orderByDesc('pinCount')
            ->limit(10)
            ->get();

        // Top 10 suburbs based on how many times they've been pinned
        $topSuburbs = DB::table('pins')
            ->select('suburb', DB::raw('count(*) as pinCount'))
            ->groupBy('suburb')
            ->orderByDesc('pinCount')
            ->limit(10)
            ->get();

        return view('admin.leaderboard.index', compact('leaderboardData', 'topSuburbs'));
    }

    public function show($id)
    {
        // Fetch specific leaderboard entry by ID
        $entry = []; // Replace with actual data fetching logic

        return view('admin.leaderboard.show', compact('entry'));
    }
}
