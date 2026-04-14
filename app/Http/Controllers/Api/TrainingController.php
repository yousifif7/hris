<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Training::with('employee');
        if ($request->filled('employee_id')) $query->where('employee_id', $request->employee_id);
        if ($request->get('expiring')) {
            $query->where('is_completed', false)->where('due_date', '<=', now()->addDays(30));
        } elseif ($request->has('completed') && $request->get('completed') === '0') {
            $query->where('is_completed', false);
        }
        return response()->json($query->orderBy('due_date')->paginate($request->get('per_page', 50)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'employee_id'      => 'required|exists:employees,id',
            'name'             => 'required|string',
            'due_date'         => 'nullable|date',
            'certificate_path' => 'nullable|string',
        ]);
        return response()->json(Training::create($data), 201);
    }

    public function complete(Training $training): JsonResponse
    {
        $training->update(['is_completed' => true, 'completed_date' => now()]);
        return response()->json($training);
    }
}
