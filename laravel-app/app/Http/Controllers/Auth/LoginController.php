<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class LoginController extends Controller
{


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        // Check if the user already exists in the database
        if(User::where('email', $googleUser->getEmail())->exists()) {
            $user = User::where('email', $googleUser->getEmail())->first();
        } else {
            // Create a new user if it doesn't exist
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'is_activated' => false,
            ]);
        }

        //if user is not activated, redirect to activation page
        if (!$user->is_activated) {
            return redirect('/activate')->with('message', 'Please activate your account.');
        }

        \auth()->login($user); // Set the session auth
        return redirect('/maps'); // Redirect to the dashboard or any other route
    }
}
