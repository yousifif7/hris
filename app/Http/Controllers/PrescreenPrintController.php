<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Sanctum\PersonalAccessToken;

class PrescreenPrintController extends Controller
{
    public function print(Request $request, Candidate $candidate): View
    {
        $token = $request->query('token');
        $accessToken = $token ? PersonalAccessToken::findToken($token) : null;
        $user = $accessToken?->tokenable;

        abort_unless(
            $user && $user->is_active && in_array($user->role, ['admin', 'hr_staff'], true),
            403
        );

        $candidate->load(['preScreening', 'category', 'assignedTo']);

        abort_if(! $candidate->preScreening, 404, 'Application form not yet submitted.');

        return view('hris.prescreen-print', compact('candidate'));
    }
}
