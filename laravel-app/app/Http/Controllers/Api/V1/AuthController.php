<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\SendAccessRequestNotificationToAdmins;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

/**
 * Class AuthController
 *
 * This controller handles authentication using Google OAuth in the API.
 * It provides methods to redirect users to Google's OAuth page and handle
 * the callback to authenticate or register users.
 */
class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirectUrl('http://localhost:8080/login/google/callback') // 👈 or any custom URI
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        $spaUrl = 'http://localhost:5174'; // URL of your SPA
        $appUrl = 'http://localhost:8080'; // URL of your Laravel app
        $googleUser = Socialite::driver('google')->stateless()
            ->redirectUrl($appUrl . '/login/google/callback')->user();

        $query = '';
        if (User::where('email', $googleUser->getEmail())->exists()) {
            if(User::where('email', $googleUser->getEmail())->first()->is_activated) {
                $user = User::where('email', $googleUser->getEmail())->first();
                $token = $user->createToken('spa')->plainTextToken;

                $query = http_build_query([
                    'token' => $token,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_activated' => $user->is_activated ? 'true' : 'false',
                ]);
            }

        } else {
            // Create a new user if it doesn't exist
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'is_activated' => false,
            ]);
            // Dispatch a job to notify admins about the new user requesting access
//            SendAccessRequestNotificationToAdmins::dispatch($user);
        }

        return redirect()->away("{$spaUrl}/login-success?{$query}");
    }
}
