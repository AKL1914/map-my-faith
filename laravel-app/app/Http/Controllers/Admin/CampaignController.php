<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;

/**
 * Class CampaignController
 *
 * This controller handles CRUD operations and management for campaigns.
 */
class CampaignController extends Controller
{
    /**
     * Display the campaign management view.
     *
     * This method returns the view for managing campaigns in the admin panel.
     *
     * @return \Illuminate\View\View The campaign management view.
     */
    public function manage()
    {
        // Logic to manage campaigns
        return view('admin.campaigns.index');
    }

    /**
     * Retrieve and return all campaigns.
     *
     * This method fetches all campaigns from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection A collection of all campaigns.
     */
    public function index()
    {
        return Campaign::all();
    }

    /**
     * Retrieve and return a specific campaign by its ID.
     *
     * This method fetches a campaign from the database or throws a 404 error if not found.
     *
     * @param  int  $id  The ID of the campaign to retrieve.
     * @return \App\Models\Campaign The requested campaign.
     */
    public function show($id)
    {
        return Campaign::findOrFail($id);
    }

    /**
     * Store a new campaign in the database.
     *
     * This method validates the request data and creates a new campaign.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing campaign data.
     * @return \App\Models\Campaign The newly created campaign.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        return Campaign::create($validated);
    }

    /**
     * Update an existing campaign in the database.
     *
     * This method validates the request data, updates the specified campaign, and ensures
     * only one campaign is active at a time.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing updated campaign data.
     * @param  int  $id  The ID of the campaign to update.
     * @return \Illuminate\Http\JsonResponse A JSON response with the updated campaign and a success message.
     */
    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        // Check if the campaign is active
        if ($validated['is_active']) {
            // Update all the campaigns to inactive
            Campaign::where('is_active', true)->update(['is_active' => false]);
        }

        $campaign->update($validated);

        return response()->json(['message' => 'Campaign updated successfully', 'campaign' => $campaign]);
    }

    /**
     * Delete a specific campaign from the database.
     *
     * This method deletes the specified campaign by its ID.
     *
     * @param  int  $id  The ID of the campaign to delete.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the operation.
     */
    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();

        return response()->json(['message' => 'Campaign deleted successfully']);
    }
}
