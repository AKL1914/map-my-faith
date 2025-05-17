<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GamePinController extends Controller
{
    public function __construct()
    {
        // Constructor logic if needed
    }

    public function index()
    {
        // Logic to fetch and display game pins
        return view('admin.gamification.gamepins.index');
    }

    public function participate($gamePinId)
    {
        // Logic to handle participation in a game pin
        return redirect()->route('admin.game-pins.index')->with('success', 'Participated successfully.');
    }
}
