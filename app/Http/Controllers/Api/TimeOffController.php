<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeOffRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimeOffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TimeOffRequest::with('employee');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('employee_id')) $query->where('employee_id', $request->employee_id);
        return response()->json($query->orderBy('created_at','desc')->paginate(25));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type'        => 'required|in:Vacation,Sick,Personal,Bereavement',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'days'        => 'required|integer|min:1',
            'notes'       => 'nullable|string',
        ]);
        return response()->json(TimeOffRequest::create($data), 201);
    }

    public function review(Request $request, TimeOffRequest $timeOffRequest): JsonResponse
    {
        $data = $request->validate(['status' => 'required|in:approved,denied']);
        $timeOffRequest->update([
            'status'      => $data['status'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        return response()->json($timeOffRequest->fresh('employee'));
    }
}
