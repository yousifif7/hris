<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHrStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Employees get read-only (GET/HEAD) access to HR routes
        if ($user->role === 'employee') {
            if (! in_array($request->method(), ['GET', 'HEAD'])) {
                return response()->json(['message' => 'Read-only access for your role.'], 403);
            }
            return $next($request);
        }

        if (! in_array($user->role, ['admin', 'hr_staff'])) {
            return response()->json(['message' => 'HR staff access required.'], 403);
        }

        return $next($request);
    }
}
