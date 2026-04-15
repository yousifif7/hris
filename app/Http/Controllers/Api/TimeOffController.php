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

    /** Employee portal — own requests only */
    public function portalIndex(): JsonResponse
    {
        $emp = auth()->user()->employee;
        if (! $emp) return response()->json([]);
        return response()->json(
            TimeOffRequest::where('employee_id', $emp->id)
                ->orderByDesc('created_at')->get()
        );
    }

    public function portalStore(Request $request): JsonResponse
    {
        $emp = auth()->user()->employee;
        if (! $emp) return response()->json(['message' => 'No employee record linked.'], 403);
        $data = $request->validate([
            'type'       => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'notes'      => 'nullable|string|max:1000',
        ]);
        $data['employee_id'] = $emp->id;
        $data['status']      = 'pending';
        return response()->json(TimeOffRequest::create($data), 201);
    }

    public function update(Request $request, TimeOffRequest $timeOffRequest): JsonResponse
    {
        $data = $request->validate([
            'type'       => 'nullable|in:Vacation,Sick,Personal,Bereavement',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'notes'      => 'nullable|string',
        ]);

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $data['days'] = (int) ceil(
                (strtotime($data['end_date']) - strtotime($data['start_date'])) / 86400
            ) + 1;
        }

        $timeOffRequest->update(array_filter($data, fn($v) => $v !== null));
        return response()->json($timeOffRequest->fresh('employee'));
    }

    public function destroy(TimeOffRequest $timeOffRequest): JsonResponse
    {
        $timeOffRequest->delete();
        return response()->json(['ok' => true]);
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
