<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::select('id', 'name', 'email', 'is_admin', 'is_activated')->get();
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

        return response()->json(['message' => 'Admin status updated.']);
    }

    public function updateActivationStatus(Request $request, User $user)
    {
        $user->is_activated = $request->boolean('is_activated');
        $user->save();

        return response()->json(['message' => 'Activation status updated.']);
    }

    public function activateAll()
    {
        User::query()->update(['is_activated' => true]);

        return response()->json(['message' => 'All users activated.']);
    }

    public function deactivateAll()
    {
        User::query()->update(['is_activated' => false]);

        return response()->json(['message' => 'All users deactivated.']);
    }
}
