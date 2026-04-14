<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\PreScreening;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreScreeningController extends Controller
{
    public function store(Request $request, Candidate $candidate): JsonResponse
    {
        $data = $request->validate([
            'education_level'     => 'required|string',
            'years_experience'    => 'required|integer|min:0',
            'licenses'            => 'nullable|string',
            'availability'        => 'required|string',
            'earliest_start_date' => 'nullable|date',
            'additional_notes'    => 'nullable|string',
        ]);

        $data['screened_by'] = auth()->id();

        $preScreening = $candidate->preScreening()
            ? $candidate->preScreening->update($data)
            : $candidate->preScreening()->create($data);

        return response()->json($candidate->fresh('preScreening'));
    }

    public function show(Candidate $candidate): JsonResponse
    {
        return response()->json($candidate->preScreening);
    }
}
