<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->attributes->get('authenticated_user');

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        if (!in_array($user->role, $roles)) {
            return response()->json([
                'error' => 'Unauthorized.',
                'message' => 'Your role (' . $user->role . ') does not have permission to access this resource.'
            ], 403);
        }

        return $next($request);
    }
}
