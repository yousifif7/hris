<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayrollRequest;
use App\Models\Employee;
use App\Models\Payroll;
use App\Support\PayrollCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Payroll::with('employee')->latest('period_end')->latest('id');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->integer('employee_id'));
        }

        return response()->json([
            'data' => $query->take((int) $request->get('per_page', 200))->get(),
        ]);
    }

    public function store(StorePayrollRequest $request): JsonResponse
    {
        $payroll = Payroll::create($this->payload($request->validated()));

        return response()->json($payroll->load('employee'), 201);
    }

    public function show(Payroll $payroll): JsonResponse
    {
        return response()->json($payroll->load('employee'));
    }

    public function update(StorePayrollRequest $request, Payroll $payroll): JsonResponse
    {
        $payroll->update($this->payload($request->validated()));

        return response()->json($payroll->fresh('employee'));
    }

    public function destroy(Payroll $payroll): JsonResponse
    {
        $payroll->delete();

        return response()->json(['message' => 'Payroll deleted']);
    }

    protected function payload(array $validated): array
    {
        $employee = Employee::findOrFail($validated['employee_id']);

        abort_if(! $employee->pay_rate, 422, 'This employee does not have a pay rate on their profile yet.');

        return [
            'employee_id' => $employee->id,
            'generated_by' => Auth::id(),
            'frequency' => $validated['frequency'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'pay_date' => $validated['pay_date'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            ...PayrollCalculator::calculate($employee, $validated),
        ];
    }
}