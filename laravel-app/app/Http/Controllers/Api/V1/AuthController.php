<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
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
    /**
     * Redirect to Google's OAuth page.
     *
     * This method generates a URL for Google's OAuth page and returns it
     * as a JSON response.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the Google OAuth URL.
     */
    public function redirectToGoogle()
    {
        return response()->json([
            'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    /**
     * Handle the callback from Google OAuth.
     *
     * This method retrieves the authenticated user's information from Google,
     * creates or updates the user in the database, and generates an API token.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the API token and user information.
     */
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
            ]
        );

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }
}
