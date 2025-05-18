<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class AdminLoginController
 *
 * This controller handles the authentication process for admin users, including
 * displaying the login form and processing login requests.
 */
class AdminLoginController extends Controller
{
    /**
     * Display the admin login form.
     *
     * If the user is already logged in and is an admin, they are redirected to the admin dashboard.
     * Otherwise, the admin login view is returned.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->is_admin) {
            // If the user is already logged in, redirect to the admin dashboard
            return redirect('/admin/dashboard');
        }
        return view('auth.admin-login');
    }

    /**
     * Handle the admin login request.
     *
     * This method validates the provided credentials and attempts to log in the user.
     * If the user is an admin, they are redirected to the admin dashboard.
     * If the user is not an admin or the credentials are invalid, appropriate error messages are returned.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing login credentials.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Check if user is admin
            if (Auth::user()->is_admin) {
                return redirect('/admin/dashboard'); // Redirect to admin dashboard
            } else {
                Auth::logout();
                return redirect()->back()->withErrors(['email' => 'You are not an admin.']);
            }
        }

        return redirect()->back()->withErrors(['email' => 'Invalid credentials.']);
    }
}
