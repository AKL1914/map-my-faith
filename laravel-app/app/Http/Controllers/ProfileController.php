<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class ProfileController
 *
 * This controller handles the display and management of user profiles.
 */
class ProfileController extends Controller
{
    /**
     * Display the profile index view.
     *
     * This method returns the main profile page of the application.
     *
     * @return \Illuminate\View\View The profile index view.
     */
    public function index()
    {
        return view('profile.index');
    }

    /**
     * Display the profile edit view.
     *
     * This method retrieves the authenticated user's data and passes it to the profile edit view.
     * If the user is not authenticated, it redirects to the home page with an error message.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View The profile edit view or a redirect response.
     */
    public function edit()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('home')->with('error', 'User not found.');
        }
        $areaGroups = config('app.area_groups');
        $pinCount = $user->pins()->count();
        return view('profile.edit', compact('user', 'areaGroups', 'pinCount'));
    }

    /**
     * Update the user's profile.
     *
     * This method validates and updates the authenticated user's profile data.
     * After updating, it redirects back with a success message.
     *
     * @param \App\Http\Requests\UpdateProfileRequest $request The validated request containing profile data.
     * @return \Illuminate\Http\RedirectResponse A redirect response with a success message.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $user->update($request->only(['area', 'group']));
        Cache::tags('users_paginated')->flush();
        return back()->with('success', 'Profile updated.');
    }
}
