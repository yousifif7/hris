<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\InterviewAvailabilitySlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InterviewAvailabilitySlotController extends Controller
{
    public function index(Candidate $candidate): JsonResponse
    {
        $slots = $candidate->interviewAvailabilitySlots()
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->get();

        return response()->json($slots);
    }

    public function store(Request $request, Candidate $candidate): JsonResponse
    {
        $data = $request->validate([
            'slots' => 'required|array|min:1',
            'slots.*.starts_at' => 'required|date|after:now',
            'slots.*.ends_at' => 'required|date',
        ]);

        $created = [];
        foreach ($data['slots'] as $slot) {
            $startsAt = Carbon::parse($slot['starts_at']);
            $endsAt = Carbon::parse($slot['ends_at']);

            if ($endsAt->lessThanOrEqualTo($startsAt)) {
                return response()->json([
                    'message' => 'Each slot end time must be after the start time.',
                ], 422);
            }

            $created[] = InterviewAvailabilitySlot::create([
                'candidate_id' => $candidate->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'created_by' => auth()->id(),
            ]);
        }

        return response()->json($created, 201);
    }

    public function destroy(InterviewAvailabilitySlot $slot): JsonResponse
    {
        if ($slot->booked_interview_id) {
            return response()->json([
                'message' => 'Booked slots cannot be deleted.',
            ], 422);
        }

        $slot->delete();

        return response()->json(['ok' => true]);
    }
}
