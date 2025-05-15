<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Default to 10 items per page
        $page = $request->input('page', 1); // Default to page 1
        $search = $request->input('search');

        // Generate a unique cache key based on page, per_page, and search
        $cacheKey = "users_paginated_page_{$page}_perpage_{$perPage}_search_" . md5($search ?? '');

        // Cache the results for 10 minutes
        $users = Cache::tags('users_paginated')->remember($cacheKey, now()->addMinutes(10), function () use ($perPage, $search) {
            $query = User::select('id', 'name', 'email', 'is_admin', 'is_activated','area','group');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            return $query->paginate($perPage);
        });

        return response()->json($users);
    }

    public function manage()
    {
        // Logic to manage campaigns
        return view('admin.users.index');
    }

    public function updateAdminStatus(Request $request, User $user)
    {
        $user->is_admin = $request->boolean('is_admin');
        $user->save();
        Cache::tags('users_paginated')->flush();
        return response()->json(['message' => 'Admin status updated.']);
    }

    public function updateActivationStatus(Request $request, User $user)
    {
        $user->is_activated = $request->boolean('is_activated');
        $user->save();
        Cache::tags('users_paginated')->flush();
        return response()->json(['message' => 'Activation status updated.']);
    }

    public function activateAll()
    {
        User::query()->where('is_admin',false)->update(['is_activated' => true]);
        Cache::tags('users_paginated')->flush();
        return response()->json(['message' => 'All users activated.']);
    }

    public function deactivateAll()
    {
        User::query()->where('is_admin',false)->update(['is_activated' => false]);
        Cache::tags('users_paginated')->flush();
        return response()->json(['message' => 'All users deactivated.']);
    }

    public function updateAreaGroup(UpdateProfileRequest $request, User $user)
    {

        $user->area = $request->input('area');
        $user->group = $request->input('group');
        $user->save();

        Cache::tags('users_paginated')->flush();

        return response()->json(['message' => 'User area and group updated.']);
    }

}
