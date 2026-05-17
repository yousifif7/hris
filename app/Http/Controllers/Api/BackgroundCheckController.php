<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\BackgroundCheck;
use App\Notifications\AdminActivityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BackgroundCheckController extends Controller
{
    public function index(Candidate $candidate): JsonResponse
    {
        return response()->json($candidate->backgroundChecks);
    }

    public function update(Request $request, BackgroundCheck $backgroundCheck): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:pending,in_progress,complete,failed',
            'notes'  => 'nullable|string',
        ]);

        if ($data['status'] === 'complete') {
            $data['completed_at'] = now();
        }

        $backgroundCheck->update($data);

        $candidate  = $backgroundCheck->candidate;
        $checkLabel = strtoupper(str_replace('_', '/', $backgroundCheck->check_type));
        $actor      = auth()->user()?->full_name ?? 'HR Staff';

        // Notify assigned HR on every individual update
        $candidate->assignedTo?->notify(new AdminActivityNotification(
            '📋 Background Check Updated',
            "{$checkLabel} check for {$candidate->full_name} marked as {$data['status']}.",
            'bg_check_updated',
            $candidate->id,
            $actor
        ));

        // Check if all BG checks are done
        if ($candidate->allBackgroundChecksComplete()) {
            $candidate->assignedTo?->notify(
                new \App\Notifications\AllBackgroundChecksComplete($candidate)
            );
        }

        return response()->json($backgroundCheck->fresh());
    }
}
