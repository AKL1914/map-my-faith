<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class IsAdmin
 *
 * Middleware to check if the authenticated user is an admin. If the user is an admin,
 * the request is allowed to proceed. Otherwise, the user is redirected to the home page
 * with an error message.
 */
class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * This method checks if the authenticated user has admin privileges. If the user is an admin,
     * the request is passed to the next middleware or controller. Otherwise, the user is redirected
     * to the home page with an error message.
     *
     * @param  Request  $request  The incoming HTTP request.
     * @param  Closure  $next  The next middleware or controller to handle the request.
     * @return Response The HTTP response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and has admin privileges.
        if (auth()->check() && auth()->user()->is_admin) {
            return $next($request); // Allow the request to proceed.
        }

        // Redirect to the home page with an error message if the user is not an admin.
        return redirect('/')->with('error', 'You are not authorized to access this page.');
    }
}
