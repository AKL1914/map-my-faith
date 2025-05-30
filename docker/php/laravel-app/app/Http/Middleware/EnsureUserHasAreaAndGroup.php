<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EnsureUserHasAreaAndGroup
 *
 * Middleware to ensure that a logged-in, non-admin user has both an area and a group assigned.
 * If the user does not have these attributes, they are redirected to the profile edit page.
 */
class EnsureUserHasAreaAndGroup
{
    /**
     * Handle an incoming request.
     *
     * This method checks if the authenticated user is a non-admin and ensures they have
     * both an area and a group assigned. If either attribute is missing, the user is
     * redirected to the profile edit page unless they are already on that page.
     *
     * @param Request $request The incoming HTTP request.
     * @param Closure $next The next middleware or controller to handle the request.
     * @return Response The HTTP response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); // Retrieve the authenticated user.

        // Only apply for logged-in, non-admin users.
        if ($user && !$user->is_admin) {
            $needsRedirect = empty($user->area) || empty($user->group); // Check if area or group is missing.

            // Redirect to the profile edit page if necessary.
            if ($needsRedirect && !$request->is('profile/edit')) {
                return redirect('/profile/edit');
            }
        }

        // Allow the request to proceed.
        return $next($request);
    }
}
