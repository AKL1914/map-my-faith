<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        if(Auth::check() && Auth::user()->is_admin) {
            // If the user is already logged in, redirect to the admin dashboard
            return redirect('/admin/dashboard');
        }
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');


        if (Auth::attempt($credentials)) {
            // Check if user is admin
            if (Auth::user()->is_admin) {
                return redirect('/admin/dashboard'); // or wherever you want
            } else {
                Auth::logout();
                return redirect()->back()->withErrors(['email' => 'You are not an admin.']);
            }
        }

        return redirect()->back()->withErrors(['email' => 'Invalid credentials.']);
    }
}

