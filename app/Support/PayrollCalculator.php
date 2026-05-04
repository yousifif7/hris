<?php

namespace App\Support;

use App\Models\Employee;

class PayrollCalculator
{
    public const FREQUENCY_DIVISORS = [
        'weekly' => 52,
        'biweekly' => 26,
        'semi_monthly' => 24,
        'monthly' => 12,
    ];

    public static function calculate(Employee $employee, array $input): array
    {
        $payType = self::normalizePayType($employee->pay_type);
        $employeeRate = round((float) ($employee->pay_rate ?? 0), 2);
        $regularHours = round((float) ($input['regular_hours'] ?? 0), 2);
        $overtimeHours = round((float) ($input['overtime_hours'] ?? 0), 2);
        $bonus = round((float) ($input['bonus'] ?? 0), 2);
        $deductions = round((float) ($input['deductions'] ?? 0), 2);
        $frequency = $input['frequency'];

        $effectiveHourlyRate = $payType === 'salary'
            ? round($employeeRate / 2080, 2)
            : $employeeRate;

        $regularPay = $payType === 'salary'
            ? round($employeeRate / self::frequencyDivisor($frequency), 2)
            : round($regularHours * $effectiveHourlyRate, 2);

        $overtimePay = round($overtimeHours * $effectiveHourlyRate * 1.5, 2);
        $grossPay = round($regularPay + $overtimePay + $bonus, 2);
        $netPay = round(max($grossPay - $deductions, 0), 2);

        return [
            'pay_type' => $payType,
            'employee_rate' => $employeeRate,
            'effective_hourly_rate' => $effectiveHourlyRate,
            'regular_hours' => $regularHours,
            'overtime_hours' => $overtimeHours,
            'regular_pay' => $regularPay,
            'overtime_pay' => $overtimePay,
            'bonus' => $bonus,
            'deductions' => $deductions,
            'gross_pay' => $grossPay,
            'net_pay' => $netPay,
        ];
    }

    public static function frequencyDivisor(string $frequency): int
    {
        return self::FREQUENCY_DIVISORS[$frequency] ?? self::FREQUENCY_DIVISORS['biweekly'];
    }

    public static function normalizePayType(?string $value): string
    {
        return strtolower(trim((string) $value)) === 'salary' ? 'salary' : 'hourly';
    }
}