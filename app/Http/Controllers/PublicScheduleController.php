<?php

namespace App\Http\Controllers;

use App\Enums\CandidateStatus;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Setting;
use App\Services\CandidateService;
use Illuminate\Http\Request;

class PublicScheduleController extends Controller
{
    public function __construct(protected CandidateService $candidateService) {}

    /**
     * GET /schedule/{token}
     * Show the self-booking calendar page to the candidate.
     */
    public function show(string $token)
    {
        $candidate = Candidate::where('schedule_token', $token)
            ->whereIn('status', [
                CandidateStatus::INVITE_SENT->value,
                CandidateStatus::NO_RESPONSE->value,
            ])
            ->firstOrFail();

        // Already booked?
        $existing = Interview::where('candidate_id', $candidate->id)
            ->where('status', 'scheduled')
            ->first();

        return view('public.schedule', [
            'candidate'   => $candidate,
            'token'       => $token,
            'existing'    => $existing,
            'company'     => Setting::get('company_name', 'Our Company'),
        ]);
    }

    /**
     * POST /schedule/{token}/book
     * Store the candidate-chosen interview slot.
     */
    public function book(Request $request, string $token)
    {
        $candidate = Candidate::where('schedule_token', $token)
            ->whereIn('status', [
                CandidateStatus::INVITE_SENT->value,
                CandidateStatus::NO_RESPONSE->value,
            ])
            ->firstOrFail();

        $data = $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        // Prevent double-booking
        $already = Interview::where('candidate_id', $candidate->id)
            ->where('status', 'scheduled')
            ->exists();

        if ($already) {
            return back()->with('error', 'You have already booked an interview.');
        }

        Interview::create([
            'candidate_id'     => $candidate->id,
            'scheduled_at'     => $data['scheduled_at'],
            'duration_minutes' => 20,
            'type'             => 'zoom',
            'status'           => 'scheduled',
        ]);

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

        return view('public.schedule-confirmed', [
            'candidate' => $candidate,
            'interview' => $interview,
            'company'   => Setting::get('company_name', 'Our Company'),
        ]);
    }
}
