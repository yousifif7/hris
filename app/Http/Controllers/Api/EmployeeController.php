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

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|email|unique:employees,email',
            'phone'           => 'nullable|string|max:50',
            'role'            => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'department'      => 'nullable|string|max:255',
            'start_date'      => 'nullable|date',
            'pay_rate'        => 'nullable|numeric|min:0',
            'pay_type'        => 'nullable|in:hourly,salary',
            'location'        => 'nullable|string|max:255',
        ]);

        $data['is_active'] = true;

        $employee = Employee::create($data);

        return response()->json($employee->load('trainings'), 201);
    }

    public function show(Employee $employee): JsonResponse
    {
        return response()->json($employee->load(['trainings', 'timeOffRequests', 'documents']));
    }

    public function update(Request $request, Employee $employee): JsonResponse
    {
        $employee->update($request->validate([
            'first_name'      => 'sometimes|string|max:255',
            'last_name'       => 'sometimes|string|max:255',
            'email'           => 'sometimes|email|unique:employees,email,'.$employee->id,
            'phone'           => 'sometimes|nullable|string|max:50',
            'role'            => 'sometimes|string|max:255',
            'employment_type' => 'sometimes|in:full_time,part_time,contract',
            'department'      => 'sometimes|nullable|string|max:255',
            'start_date'      => 'sometimes|nullable|date',
            'pay_rate'        => 'sometimes|nullable|numeric|min:0',
            'pay_type'        => 'sometimes|nullable|in:hourly,salary',
            'location'        => 'sometimes|nullable|string|max:255',
            'is_active'       => 'sometimes|boolean',
        ]));
        return response()->json($employee->fresh('trainings'));
    }

    public function destroy(Employee $employee): JsonResponse
    {
        // $employee->update(['is_active' => false]);
        $employee->delete();
        return response()->json(['message' => 'Employee removed']);
    }
}
