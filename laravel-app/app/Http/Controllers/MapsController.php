<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class MapsController extends Controller
{
    public function index()
    {
        $campaign = Campaign::where('is_active', true)->first();
        return view('maps.index', compact('campaign'));
    }

    public function show($id)
    {
        return view('maps.show', ['id' => $id]);
    }

    public function create()
    {
        return view('maps.create');
    }

    public function store(Request $request)
    {
        // Validate and store the pin data
        // Pin::create($request->all());
        return redirect()->route('maps.index');
    }
}
