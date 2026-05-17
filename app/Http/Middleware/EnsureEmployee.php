<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // HR staff and admins can also access employee portal endpoints
        if (! in_array($user->role, ['admin', 'hr_staff', 'employee'])) {
            return response()->json(['message' => 'Access denied.'], 403);
        }

        return $next($request);
    }
}
