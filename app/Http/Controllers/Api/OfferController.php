<?php

namespace App\Http\Controllers\Api;

use App\Enums\CandidateStatus;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Offer;
use App\Services\CandidateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OfferController extends Controller
{
    public function __construct(protected CandidateService $service) {}

    public function index(): JsonResponse
    {
        $offers = Offer::with('candidate.category')
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return response()->json($offers);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'candidate_id'       => 'required|exists:candidates,id',
            'pay_rate'           => 'required|numeric|min:0',
            'pay_type'           => 'nullable|string',
            'employment_type'    => 'required|string',
            'location'           => 'nullable|string',
            'required_documents' => 'nullable|string',
            'deadline_days'      => 'nullable|integer|min:1',
            'orientation_date'   => 'nullable|date',
            'start_date'         => 'nullable|date',
            'notes'              => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();
        $data['status'] = 'sent';
        $data['sent_at'] = now();
        $data['token']   = Str::uuid()->toString(); // unique offer acceptance token

        $offer = Offer::create($data);

        // Update candidate status
        $this->service->changeStatus(
            Candidate::find($data['candidate_id']),
            CandidateStatus::OFFER_LETTER
        );

        return response()->json($offer->load('candidate'), 201);
    }

    public function update(Request $request, Offer $offer): JsonResponse
    {
        $data = $request->validate([
            'pay_rate'           => 'nullable|numeric|min:0',
            'pay_type'           => 'nullable|string',
            'employment_type'    => 'nullable|string',
            'location'           => 'nullable|string',
            'required_documents' => 'nullable|string',
            'deadline_days'      => 'nullable|integer|min:1',
            'start_date'         => 'nullable|date',
            'orientation_date'   => 'nullable|date',
        ]);

        $offer->update(array_filter($data, fn($v) => $v !== null));
        return response()->json($offer->fresh('candidate'));
    }

    public function destroy(Offer $offer): JsonResponse
    {
        $offer->delete();
        return response()->json(['ok' => true]);
    }

    public function respond(Request $request, Offer $offer): JsonResponse
    {
        $data = $request->validate([
            'response'         => 'required|in:accepted,declined',
            'orientation_date' => 'nullable|date|after:today',
            'start_date'       => 'nullable|date|after:today',
        ]);

        $updateFields = [
            'status'       => $data['response'],
            'responded_at' => now(),
        ];

        if ($data['response'] === 'accepted') {
            if (!empty($data['orientation_date'])) {
                $updateFields['orientation_date'] = $data['orientation_date'];
            }
            if (!empty($data['start_date'])) {
                $updateFields['start_date'] = $data['start_date'];
            }
        }

        $offer->update($updateFields);

        $newStatus = $data['response'] === 'accepted'
            ? CandidateStatus::PRE_ONBOARD_DOCUMENTS
            : CandidateStatus::APPLICANT_DECLINED;

        $this->service->changeStatus($offer->candidate, $newStatus);

        return response()->json($offer->fresh('candidate'));
    }
}
