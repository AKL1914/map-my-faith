<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GamePin;
use Illuminate\Http\Request;

/**
 * Class GamePinController
 *
 * This controller handles CRUD operations and additional functionalities for managing game pins
 * in the admin panel.
 */
class GamePinController extends Controller
{
    /**
     * GamePinController constructor.
     *
     * This constructor can be used to initialize middleware or other dependencies.
     */
    public function __construct()
    {
        // Constructor logic if needed
    }

    /**
     * Display the game pins management view.
     *
     * This method returns the view for managing game pins in the admin panel.
     *
     * @return \Illuminate\View\View The game pins management view.
     */
    public function index()
    {
        // Logic to fetch and display game pins
        return view('admin.gamification.gamepins.index');
    }

    /**
     * Store a new game pin in the database.
     *
     * This method validates the request data and creates a new game pin.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing game pin data.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'event_id' => 'required|integer',
        ]);

        // Store the game pin in the database
        GamePin::create($validated);

        return response()->json(['message' => 'Game pin created successfully.']);
    }

    /**
     * Delete a specific game pin from the database.
     *
     * This method ensures that a game pin cannot be deleted if its associated event is active.
     *
     * @param  \App\Models\GamePin  $gamePin  The game pin to delete.
     * @param  \Illuminate\Http\Request  $request  The HTTP request instance.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function destroy(GamePin $gamePin, Request $request)
    {
        // Check if the associated event is active
        if ($gamePin->event->is_active) {
            return response()->json(['message' => 'Event is active. Cannot delete game pin.'], 403);
        }

        // Delete the game pin
        $gamePin->delete();

        return response()->json(['message' => 'Game pin deleted successfully.']);
    }
}
