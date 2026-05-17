<?php

namespace App\Http\Controllers;

use App\Enums\CandidateStatus;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\InterviewAvailabilitySlot;
use App\Models\Setting;
use App\Services\CandidateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicScheduleController extends Controller
{
    public function __construct(protected CandidateService $candidateService) {}

    /**
     * GET /schedule/{token}
     * Show the self-booking calendar page to the candidate.
     */
    public function show(string $token)
    {
        $timezone = config('app.timezone', 'UTC');
        $nowUtc = now()->utc();

        $candidate = Candidate::where('schedule_token', $token)
            ->whereIn('status', [
                CandidateStatus::INVITE_SENT->value,
                CandidateStatus::NO_RESPONSE->value,
            ])
            ->firstOrFail();

        // Already booked? Only treat FUTURE interviews as blocking.
        $existing = Interview::where('candidate_id', $candidate->id)
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', $nowUtc)
            ->orderBy('scheduled_at')
            ->first();

        $existingLocal = $existing?->scheduled_at?->copy()->timezone($timezone);

        $slots = InterviewAvailabilitySlot::where('candidate_id', $candidate->id)
            ->whereNull('booked_interview_id')
            ->where('starts_at', '>=', $nowUtc)
            ->orderBy('starts_at')
            ->get();

        return view('public.schedule', [
            'candidate'   => $candidate,
            'token'       => $token,
            'existing'    => $existing,
            'existingLocal' => $existingLocal,
            'slots'       => $slots,
            'timezone'    => $timezone,
            'company'     => Setting::get('company_name', 'Our Company'),
        ]);
    }

    /**
     * POST /schedule/{token}/book
     * Store the candidate-chosen interview slot.
     */
    public function book(Request $request, string $token)
    {
        $nowUtc = now()->utc();

        $candidate = Candidate::where('schedule_token', $token)
            ->whereIn('status', [
                CandidateStatus::INVITE_SENT->value,
                CandidateStatus::NO_RESPONSE->value,
            ])
            ->firstOrFail();

        $data = $request->validate([
            'slot_id' => 'required|integer|exists:interview_availability_slots,id',
        ]);

        // Prevent double-booking against future scheduled interviews only.
        $already = Interview::where('candidate_id', $candidate->id)
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', $nowUtc)
            ->exists();

        if ($already) {
            return back()->with('error', 'You have already booked an interview.');
        }

        $booked = DB::transaction(function () use ($candidate, $data) {
            $slot = InterviewAvailabilitySlot::where('id', $data['slot_id'])
                ->where('candidate_id', $candidate->id)
                ->whereNull('booked_interview_id')
                ->lockForUpdate()
                ->first();

            if (! $slot || $slot->starts_at->isPast()) {
                return false;
            }

            $duration = max(5, $slot->starts_at->diffInMinutes($slot->ends_at));

            $interview = Interview::create([
                'candidate_id'     => $candidate->id,
                'scheduled_at'     => $slot->starts_at,
                'duration_minutes' => $duration,
                'type'             => Setting::get('default_interview_type', 'zoom'),
                'status'           => 'scheduled',
            ]);

            $slot->update(['booked_interview_id' => $interview->id]);

            return true;
        });

        if (! $booked) {
            return back()->with('error', 'That interview slot is no longer available. Please choose another one.');
        }

        $this->candidateService->changeStatus($candidate, CandidateStatus::INTERVIEW_SCHEDULED);

        return redirect("/schedule/{$token}/confirmed");
    }

    /**
     * GET /schedule/{token}/confirmed
     * Confirmation page after booking.
     */
    public function confirmed(string $token)
    {
        $candidate = Candidate::where('schedule_token', $token)->firstOrFail();

        $interview = Interview::where('candidate_id', $candidate->id)
            ->where('status', 'scheduled')
            ->latest()
            ->firstOrFail();

        $timezone = config('app.timezone', 'UTC');
        $scheduledLocal = $interview->scheduled_at?->copy()->timezone($timezone);
        $displayType = ucfirst(str_replace('_', ' ', $interview->type ?: Setting::get('default_interview_type', 'zoom')));

        return view('public.schedule-confirmed', [
            'candidate' => $candidate,
            'interview' => $interview,
            'scheduledLocal' => $scheduledLocal,
            'timezone' => $timezone,
            'displayType' => $displayType,
            'company'   => Setting::get('company_name', 'Our Company'),
        ]);
    }
}
