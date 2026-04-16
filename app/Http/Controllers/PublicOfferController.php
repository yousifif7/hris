<?php

namespace App\Http\Controllers;

use App\Enums\CandidateStatus;
use App\Models\Offer;
use App\Notifications\AdminActivityNotification;
use App\Services\CandidateService;
use Illuminate\Http\Request;

class PublicOfferController extends Controller
{
    public function __construct(protected CandidateService $service) {}

    /**
     * Show the public offer acceptance page.
     * GET /offer/{token}
     */
    public function show(string $token)
    {
        $offer = Offer::where('token', $token)
            ->with('candidate.category')
            ->firstOrFail();

        // Mark as viewed if first time
        if (! $offer->viewed_at && $offer->status === 'sent') {
            $offer->update(['status' => 'viewed', 'viewed_at' => now()]);
        }

        $candidate = $offer->candidate;
        $company   = \App\Models\Setting::get('company_name', 'McCrory Center');

        return view('public.offer', compact('offer', 'candidate', 'company'));
    }

    /**
     * Candidate accepts or declines the offer via the public page.
     * POST /offer/{token}/respond
     */
    public function respond(Request $request, string $token)
    {
        $offer = Offer::where('token', $token)
            ->with('candidate')
            ->firstOrFail();

        $data = $request->validate([
            'response'         => 'required|in:accepted,declined',
            'orientation_date' => 'nullable|date|after:today',
            'start_date'       => 'nullable|date|after:today',
            'signature'        => 'nullable|string|max:200',
        ]);

        if (in_array($offer->status, ['accepted', 'declined'])) {
            return redirect()->back()->with('info', 'This offer has already been responded to.');
        }

        $updateFields = [
            'status'       => $data['response'],
            'responded_at' => now(),
        ];

        if ($data['response'] === 'accepted') {
            if (! empty($data['orientation_date'])) {
                $updateFields['orientation_date'] = $data['orientation_date'];
            }
            if (! empty($data['start_date'])) {
                $updateFields['start_date'] = $data['start_date'];
            }
            if (! empty($data['signature'])) {
                $updateFields['notes'] = 'Signed by: ' . $data['signature'] . ' on ' . now()->format('M d, Y');
            }
        }

        $offer->update($updateFields);

        // Update candidate status
        $newStatus = $data['response'] === 'accepted'
            ? CandidateStatus::OFFER_ACCEPTED
            : CandidateStatus::APPLICANT_DECLINED;

        $this->service->changeStatus($offer->candidate, $newStatus);

        // Notify admins immediately
        $candidate = $offer->candidate;
        $actionLabel = $data['response'] === 'accepted' ? 'accepted' : 'declined';

        $notification = new AdminActivityNotification(
            $data['response'] === 'accepted' ? '✅ Offer Accepted' : '❌ Offer Declined',
            "{$candidate->full_name} has {$actionLabel} the offer.",
            $data['response'] === 'accepted' ? 'offer_accepted' : 'offer_declined',
            $candidate->id,
            $candidate->full_name
        );

        \App\Models\User::where('role', 'admin')
            ->where('is_active', true)
            ->get()
            ->each(fn($admin) => $admin->notify($notification));

        $accepted = $data['response'] === 'accepted';
        $company  = \App\Models\Setting::get('company_name', 'McCrory Center');

        return view('public.offer-response', compact('offer', 'candidate', 'company', 'accepted'));
    }
}
