<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCandidate
{
    /**
     * Allow only users tied to a Candidate record (role=candidate, plus admins for support).
     * Requests are expected to be authenticated via the bearer token issued at /api/login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->reject($request, 'Unauthenticated.', 401);
        }

        if (! in_array($user->role, ['candidate', 'admin'], true)) {
            return $this->reject($request, 'Access denied.', 403);
        }

        if ($user->role === 'candidate' && ! $user->candidate) {
            return $this->reject($request, 'No candidate record linked to this account.', 403);
        }

        return $next($request);
    }

    protected function reject(Request $request, string $message, int $status): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }
        return redirect('/login');
    }
}
