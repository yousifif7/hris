<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id',
        'generated_by',
        'frequency',
        'period_start',
        'period_end',
        'pay_date',
        'pay_type',
        'employee_rate',
        'effective_hourly_rate',
        'regular_hours',
        'overtime_hours',
        'regular_pay',
        'overtime_pay',
        'bonus',
        'deductions',
        'gross_pay',
        'net_pay',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'pay_date' => 'date',
            'employee_rate' => 'decimal:2',
            'effective_hourly_rate' => 'decimal:2',
            'regular_hours' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'regular_pay' => 'decimal:2',
            'overtime_pay' => 'decimal:2',
            'bonus' => 'decimal:2',
            'deductions' => 'decimal:2',
            'gross_pay' => 'decimal:2',
            'net_pay' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}