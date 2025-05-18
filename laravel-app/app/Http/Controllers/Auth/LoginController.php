<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * Class LoginController
 *
 * This controller handles user authentication, including traditional login
 * and login via Google OAuth. It also manages session regeneration and user
 * redirection based on authentication status.
 */
class LoginController extends Controller
{
    /**
     * Handle user login with email and password.
     *
     * This method validates the provided credentials and attempts to log in the user.
     * If successful, the session is regenerated, and the user is redirected to the intended page.
     * If the credentials are invalid, an error message is returned.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing login credentials.
     * @return \Illuminate\Http\RedirectResponse A redirect response to the intended page or back with errors.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    /**
     * Redirect to Google's OAuth page.
     *
     * This method initiates the Google OAuth login process by redirecting the user
     * to Google's authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse A redirect response to Google's OAuth page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google OAuth.
     *
     * This method retrieves the authenticated user's information from Google,
     * checks if the user exists in the database, and logs them in. If the user
     * does not exist, a new user is created. If the user is not activated, they
     * are redirected to the activation page.
     *
     * @return \Illuminate\Http\RedirectResponse A redirect response to the appropriate page based on user status.
     */
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Check if the user already exists in the database
        if (User::where('email', $googleUser->getEmail())->exists()) {
            $user = User::where('email', $googleUser->getEmail())->first();
        } else {
            // Create a new user if it doesn't exist
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'is_activated' => false,
            ]);
        }

        // If the user is not activated, redirect to the activation page
        if (!$user->is_activated) {
            return redirect('/activate')->with('message', 'Please activate your account.');
        }

        Auth::login($user); // Set the session auth
        return redirect('/maps'); // Redirect to the dashboard or any other route
    }
}
