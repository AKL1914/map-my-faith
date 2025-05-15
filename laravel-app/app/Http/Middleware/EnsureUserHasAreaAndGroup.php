<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasAreaAndGroup
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only apply for logged-in, non-admin users
        if ($user && !$user->is_admin) {
            $needsRedirect = empty($user->area) || empty($user->group);

            if ($needsRedirect && !$request->is('profile/edit')) {
                return redirect('/profile/edit');
            }
        }

        return $next($request);
    }
}
