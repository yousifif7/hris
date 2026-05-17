<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicApplyController extends Controller
{
    /**
     * Serve the public application form.
     * URL is masked — only works when the correct token is supplied.
     */
    public function show(string $token): View
    {
        $valid = Setting::get('apply_token');

        // Lazy-generate token if it has never been set
        if (! $valid) {
            $valid = Str::uuid()->toString();
            Setting::set('apply_token', $valid);
        }

        // Constant-time comparison to prevent timing attacks
        if (! hash_equals($valid, $token)) {
            abort(404);
        }

        return view('public.apply');
    }
}
