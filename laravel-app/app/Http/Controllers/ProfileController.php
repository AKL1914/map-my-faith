<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function edit()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('home')->with('error', 'User not found.');
        }
        $areaGroups = config('app.area_groups');
        $pinCount = $user->pins()->count();
        return view('profile.edit', compact('user','areaGroups', 'pinCount'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $user->update($request->only(['area', 'group']));

        return back()->with('success', 'Profile updated.');
    }
}
