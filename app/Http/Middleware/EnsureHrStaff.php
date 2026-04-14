<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHrStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->user()?->role, ['admin', 'hr_staff'])) {
            return response()->json(['message' => 'HR staff access required.'], 403);
        }

        return $next($request);
    }
}
