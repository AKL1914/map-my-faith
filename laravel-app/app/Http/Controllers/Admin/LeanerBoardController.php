<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeanerBoardController extends Controller
{
    public function index()
    {
        // Fetch top 10 leaderboard entries based on the number of pins
        $leaderboardData = DB::table('pins')
            ->join('users', 'pins.user_id', '=', 'users.id') // Join with the users table
            ->select('users.name', DB::raw('count(pins.id) as pinCount'))
            ->groupBy('users.id')
            ->orderByDesc('pinCount') // Sort by pin count in descending order
            ->limit(10) // Limit to top 10
            ->get();

        return view('admin.leaderboard.index', compact('leaderboardData'));
    }

    public function show($id)
    {
        // Fetch specific leaderboard entry by ID
        $entry = []; // Replace with actual data fetching logic

        return view('admin.leaderboard.show', compact('entry'));
    }
}
