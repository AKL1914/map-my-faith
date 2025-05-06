<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
//        dd('s');
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');


        if (Auth::attempt($credentials)) {
//            dd('ss1');
            // Check if user is admin
            if (Auth::user()->is_admin) {
//                dd('sss');
                return redirect('/admin/dashboard'); // or wherever you want
            } else {
                Auth::logout();
                return redirect()->back()->withErrors(['email' => 'You are not an admin.']);
            }
        }
//        dd('ss');

        return redirect()->back()->withErrors(['email' => 'Invalid credentials.']);
    }
}

