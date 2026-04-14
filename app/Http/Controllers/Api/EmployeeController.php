<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Employee::with('trainings');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('first_name','like',"%{$s}%")
                ->orWhere('last_name','like',"%{$s}%")
                ->orWhere('role','like',"%{$s}%"));
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        return response()->json($query->orderBy('last_name')->paginate(25));
    }

    public function show(Employee $employee): JsonResponse
    {
        return response()->json($employee->load(['trainings', 'timeOffRequests', 'documents']));
    }

    public function update(Request $request, Employee $employee): JsonResponse
    {
        $employee->update($request->validate([
            'first_name'      => 'sometimes|string',
            'last_name'       => 'sometimes|string',
            'phone'           => 'sometimes|nullable|string',
            'role'            => 'sometimes|string',
            'employment_type' => 'sometimes|string',
            'department'      => 'sometimes|nullable|string',
            'location'        => 'sometimes|nullable|string',
            'is_active'       => 'sometimes|boolean',
        ]));
        return response()->json($employee->fresh());
    }
}
