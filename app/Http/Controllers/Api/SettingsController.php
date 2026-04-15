<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutomationRule;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'company_name'           => Setting::get('company_name', 'Wellness Behavioral Health'),
            'timezone'               => Setting::get('timezone', 'America/Detroit'),
            'interview_duration'     => Setting::get('interview_duration', 20),
            'offer_deadline'         => Setting::get('offer_deadline', 20),
            'followup_days'          => Setting::get('followup_days', 5),
            'queue_days'             => Setting::get('queue_days', 10),
            'default_interview_type' => Setting::get('default_interview_type', 'zoom'),
            'zoom_link'              => Setting::get('zoom_link', ''),
            'office_address'         => Setting::get('office_address', ''),
            'door_access_info'       => Setting::get('door_access_info', ''),
            'wifi_password'          => Setting::get('wifi_password', ''),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        foreach ($request->all() as $key => $value) {
            Setting::set($key, $value);
        }
        return response()->json(['message' => 'Settings updated']);
    }

    public function automationRules(): JsonResponse
    {
        return response()->json(AutomationRule::all());
    }

    public function emailTemplates(): JsonResponse
    {
        return response()->json(EmailTemplate::all());
    }

    public function updateEmailTemplate(Request $request, EmailTemplate $template): JsonResponse
    {
        $template->update($request->validate([
            'subject' => 'required|string',
            'body'    => 'required|string',
        ]));
        return response()->json($template);
    }

    public function applyLink(): JsonResponse
    {
        $token = Setting::get('apply_token');

        if (! $token) {
            $token = Str::uuid()->toString();
            Setting::set('apply_token', $token);
        }

        return response()->json([
            'url'   => url("/apply/{$token}"),
            'token' => $token,
        ]);
    }

    public function regenerateApplyLink(): JsonResponse
    {
        $token = Str::uuid()->toString();
        Setting::set('apply_token', $token);

        return response()->json([
            'url'   => url("/apply/{$token}"),
            'token' => $token,
        ]);
    }

    public function hrTeam(): JsonResponse
    {
        $staff = User::where('role', 'hr_staff')->where('is_active', true)
            ->withCount('assignedCandidates as active_candidates_count')
            ->orderBy('round_robin_order')
            ->get()
            ->map(fn($u) => array_merge($u->toArray(), [
                'name' => "{$u->first_name} {$u->last_name}",
            ]));
        return response()->json($staff);
    }
}
