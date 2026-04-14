<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutomationRule;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'company_name'    => Setting::get('company_name', 'Wellness Behavioral Health'),
            'timezone'        => Setting::get('timezone', 'America/Detroit'),
            'interview_duration' => Setting::get('interview_duration', 20),
            'offer_deadline'  => Setting::get('offer_deadline', 20),
            'followup_days'   => Setting::get('followup_days', 5),
            'queue_days'      => Setting::get('queue_days', 10),
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

    public function hrTeam(): JsonResponse
    {
        $staff = User::where('role', 'hr_staff')->where('is_active', true)
            ->withCount('assignedCandidates')
            ->orderBy('round_robin_order')
            ->get();
        return response()->json($staff);
    }
}
