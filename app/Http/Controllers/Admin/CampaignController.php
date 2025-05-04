<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function manage()
    {
        // Logic to manage campaigns
        return view('admin.campaigns.index');

    }
    // Show all campaigns
    public function index()
    {
        return Campaign::all();
    }

    // Show a specific campaign
    public function show($id)
    {
        return Campaign::findOrFail($id);
    }

    // Store a new campaign
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        return Campaign::create($validated);
    }

    // Update an existing campaign
    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);
        // Check if the campaign is active
        if ($validated['is_active']) {
            // update all the campaigns to inactive
            Campaign::where('is_active', true)->update(['is_active' => false]);
        }

        $campaign->update($validated);

        return response()->json(['message' => 'Campaign updated successfully', 'campaign' => $campaign]);
    }

    // Delete a campaign
    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();

        return response()->json(['message' => 'Campaign deleted successfully']);
    }
}
