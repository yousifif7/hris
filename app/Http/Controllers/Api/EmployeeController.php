<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /** Maps 'Admin', 'HR Staff', 'Employee' → user.role values */
    private const DEPT_ROLE_MAP = [
        'Admin'    => 'admin',
        'HR Staff' => 'hr_staff',
        'Employee' => 'employee',
    ];

    private function flattenUser(User $u): array
    {
        $emp = $u->employee;
        return [
            'id'              => $emp?->id,
            'user_id'         => $u->id,
            'first_name'      => $u->first_name,
            'last_name'       => $u->last_name,
            'email'           => $u->email,
            'phone'           => $emp?->phone         ?? null,
            'user_role'       => $u->role,
            'role'            => $emp?->role           ?? '',
            'department'      => $emp?->department     ?? '',
            'employment_type' => $emp?->employment_type ?? '',
            'start_date'      => $emp?->start_date ? $emp->start_date->toDateString() : null,
            'pay_rate'        => $emp?->pay_rate,
            'pay_type'        => $emp?->pay_type       ?? '',
            'location'        => $emp?->location       ?? '',
            'is_active'       => $u->is_active,
            'trainings'       => $emp?->trainings?->toArray() ?? [],
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $query = User::with(['employee' => fn($q) => $q->with('trainings')])
            ->where('is_active', true);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('first_name', 'like', "%{$s}%")
                ->orWhere('last_name',  'like', "%{$s}%")
                ->orWhere('email',      'like', "%{$s}%"));
        }

        $users = $query->orderBy('last_name')->get();
        $flat  = $users->map(fn($u) => $this->flattenUser($u))->values();

        return response()->json(['data' => $flat, 'total' => $flat->count()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|email|unique:employees,email|unique:users,email',
            'password'        => 'required|string|min:8',
            'phone'           => 'nullable|string|max:50',
            'role'            => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'department'      => 'required|in:Admin,HR Staff,Employee',
            'start_date'      => 'nullable|date',
            'pay_rate'        => 'nullable|numeric|min:0',
            'pay_type'        => 'nullable|in:hourly,salary',
            'location'        => 'nullable|string|max:255',
        ]);

        $data['is_active'] = true;
        $userRole = self::DEPT_ROLE_MAP[$data['department']];

        // Create a User account so the employee can log in
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => $userRole,
            'is_active'  => true,
        ]);

        unset($data['password']);
        $data['user_id'] = $user->id;
        $employee = Employee::create($data);

        return response()->json($employee->load('trainings'), 201);
    }

    public function show(Employee $employee): JsonResponse
    {
        return response()->json($employee->load(['trainings', 'timeOffRequests', 'documents']));
    }

    public function update(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'first_name'      => 'sometimes|string|max:255',
            'last_name'       => 'sometimes|string|max:255',
            'email'           => 'sometimes|email|unique:employees,email,'.$employee->id,
            'password'        => 'sometimes|nullable|string|min:8',
            'phone'           => 'sometimes|nullable|string|max:50',
            'role'            => 'sometimes|string|max:255',
            'employment_type' => 'sometimes|in:full_time,part_time,contract',
            'department'      => 'sometimes|in:Admin,HR Staff,Employee',
            'start_date'      => 'sometimes|nullable|date',
            'pay_rate'        => 'sometimes|nullable|numeric|min:0',
            'pay_type'        => 'sometimes|nullable|in:hourly,salary',
            'location'        => 'sometimes|nullable|string|max:255',
            'is_active'       => 'sometimes|boolean',
        ]);

        // Sync password change to the linked User account
        if (! empty($validated['password'])) {
            if ($employee->user_id) {
                User::where('id', $employee->user_id)->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }
        }
        unset($validated['password']);

        // Sync department → user.role when department changes
        if (isset($validated['department']) && $employee->user_id) {
            $newRole = self::DEPT_ROLE_MAP[$validated['department']] ?? null;
            if ($newRole) {
                User::where('id', $employee->user_id)->update(['role' => $newRole]);
            }
        }

        // Sync name/email changes to the linked User account
        $userFields = array_filter([
            'first_name' => $validated['first_name'] ?? null,
            'last_name'  => $validated['last_name']  ?? null,
            'email'      => $validated['email']       ?? null,
        ]);
        if ($userFields && $employee->user_id) {
            User::where('id', $employee->user_id)->update($userFields);
        }

        $employee->update($validated);
        return response()->json($employee->fresh('trainings'));
    }

    public function destroy(Employee $employee): JsonResponse
    {
        // Also remove the linked User account so they can no longer log in
        if ($employee->user_id) {
            User::where('id', $employee->user_id)->delete();
        }
        $employee->delete();
        return response()->json(['message' => 'Employee removed']);
    }
}
