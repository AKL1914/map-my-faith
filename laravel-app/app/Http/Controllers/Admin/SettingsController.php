<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function manage()
    {
        return view('admin.settings.index');
    }
    // Show all settings
    public function index()
    {
        $settings = Setting::all();
        return response()->json($settings);
    }

    // Store new setting
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:settings,name',
            'value' => 'nullable|string',
            'note' => 'nullable|string',
            'enabled' => 'boolean',
        ]);

        $setting = Setting::create($validated);

        return response()->json($setting, 201);
    }

    // Update existing setting
    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'value' => 'nullable|string',
            'note' => 'nullable|string',
            'enabled' => 'boolean',
        ]);

        $setting->update($validated);

        return response()->json($setting);
    }

    // Delete setting
    public function destroy(Setting $setting)
    {
        $setting->delete();
        return response()->json(['message' => 'Setting deleted']);
    }
}
