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
            'app_url'                => Setting::get('app_url', ''),
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
            // SMTP
            'smtp_host'              => Setting::get('smtp_host', ''),
            'smtp_port'              => Setting::get('smtp_port', '587'),
            'smtp_username'          => Setting::get('smtp_username', ''),
            'smtp_password'          => Setting::get('smtp_password', ''),
            'smtp_from_email'        => Setting::get('smtp_from_email', ''),
            'smtp_from_name'         => Setting::get('smtp_from_name', ''),
            'smtp_encryption'        => Setting::get('smtp_encryption', 'tls'),
            // SMS / Twilio
            'twilio_account_sid'     => Setting::get('twilio_account_sid', ''),
            'twilio_auth_token'      => Setting::get('twilio_auth_token', ''),
            'twilio_from_number'     => Setting::get('twilio_from_number', ''),
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
        return response()->json(EmailTemplate::orderBy('category')->orderBy('name')->get());
    }

    /**
     * Return a single email template.
     */
    public function showEmailTemplate(EmailTemplate $template): JsonResponse
    {
        return response()->json($template);
    }

    public function updateEmailTemplate(Request $request, EmailTemplate $template): JsonResponse
    {
        $template->update($request->validate([
            'name'      => 'sometimes|required|string|max:100',
            'subject'   => 'required|string',
            'body'      => 'required|string',
            'body_html' => 'nullable|string',
            'category'  => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]));
        return response()->json($template);
    }

    public function createEmailTemplate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'subject'   => 'required|string',
            'body'      => 'required|string',
            'body_html' => 'nullable|string',
            'category'  => 'nullable|string|max:50',
        ]);

        $data['slug']      = Str::slug($data['name']) . '-' . Str::random(4);
        $data['is_active'] = true;

        $template = EmailTemplate::create($data);
        return response()->json($template, 201);
    }

    public function destroyEmailTemplate(EmailTemplate $template): JsonResponse
    {
        $template->delete();
        return response()->json(['ok' => true]);
    }

    /**
     * Return available template tokens for the frontend token-picker.
     */
    public function emailTokens(): JsonResponse
    {
        return response()->json(EmailTemplate::availableTokens());
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
