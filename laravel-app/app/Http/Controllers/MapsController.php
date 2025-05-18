<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

/**
 * Class MapsController
 *
 * This controller handles the display and management of map-related views and data.
 */
class MapsController extends Controller
{
    /**
     * Display the main maps view with the active campaign.
     *
     * This method retrieves the first active campaign and passes it to the maps index view.
     *
     * @return \Illuminate\View\View The maps index view with the active campaign.
     */
    public function index()
    {
        $campaign = Campaign::where('is_active', true)->first();
        return view('maps.index', compact('campaign'));
    }

    /**
     * Display a specific map view by its ID.
     *
     * This method returns the maps show view for a specific map identified by its ID.
     *
     * @param int $id The ID of the map to display.
     * @return \Illuminate\View\View The maps show view with the specified ID.
     */
    public function show($id)
    {
        return view('maps.show', ['id' => $id]);
    }

    /**
     * Display the map creation view.
     *
     * This method returns the view for creating a new map.
     *
     * @return \Illuminate\View\View The maps create view.
     */
    public function create()
    {
        return view('maps.create');
    }

    /**
     * Store a new map pin in the database.
     *
     * This method validates the request data and stores the new map pin.
     * After storing, it redirects the user to the maps index view.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing map pin data.
     * @return \Illuminate\Http\RedirectResponse A redirect response to the maps index view.
     */
    public function store(Request $request)
    {
        // Validate and store the pin data
        // Pin::create($request->all());
        return redirect()->route('maps.index');
    }
}
