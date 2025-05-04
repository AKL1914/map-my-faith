<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pin;
use App\Models\Campaign;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
//        $campaignId = $request->query('campaign');
//
//        $pins = Pin::with('campaign')
//            ->when($campaignId, fn($query) => $query->where('campaign_id', $campaignId))
//            ->get();
//
//        $campaigns = Campaign::all();

//        return view('admin.dashboard', compact('pins', 'campaigns', 'campaignId'));
        return view('admin.dashboard');
    }
}
