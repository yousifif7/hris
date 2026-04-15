<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CandidateReference;
use App\Jobs\SendReferenceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferenceController extends Controller
{
    public function index(Candidate $candidate): JsonResponse
    {
        return response()->json($candidate->references);
    }

    public function store(Request $request, Candidate $candidate): JsonResponse
    {
        $data = $request->validate([
            'reference_name'  => 'required|string',
            'reference_email' => 'required|email',
            'reference_phone' => 'nullable|string',
            'relationship'    => 'nullable|string',
        ]);

        $ref = $candidate->references()->create($data);

        // Auto-send reference request email
        SendReferenceRequest::dispatch($ref);

        return response()->json($ref, 201);
    }

    public function destroy(Candidate $candidate, CandidateReference $reference): JsonResponse
    {
        $reference->delete();
        return response()->json(['ok' => true]);
    }

    public function submitResponse(Request $request, CandidateReference $reference): JsonResponse
    {
        $data = $request->validate(['response' => 'required|string']);
        $reference->update([
            'response'    => $data['response'],
            'status'      => 'received',
            'received_at' => now(),
        ]);

        // Notify HR
        $reference->candidate->assignedTo?->notify(
            new \App\Notifications\ReferenceReceived($reference)
        );

        return response()->json($reference->fresh());
    }
}
