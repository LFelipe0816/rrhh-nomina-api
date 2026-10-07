<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'base_salary',
        'overtime_hours',
        'overtime_rate',
        'bonus',
        'deductions',
        'tax_percentage',
        'social_security',
        'other_deductions',
        'gross_pay',
        'total_deductions',
        'net_pay',
        'notes',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deductions' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'social_security' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->gross_pay = $item->calculateGrossPay();
            $item->total_deductions = $item->calculateTotalDeductions();
            $item->net_pay = $item->gross_pay - $item->total_deductions;
        });
    }

    protected function calculateGrossPay(): float
    {
        $baseSalary = (float) $this->base_salary;
        $overtimeHours = (float) $this->overtime_hours;
        $overtimeRate = (float) ($this->overtime_rate ?: 1.5);
        $bonus = (float) $this->bonus;

        // Overtime pay: (base_salary / 160 hours) * overtime_hours * overtime_rate
        $hourlyRate = $baseSalary / 160;
        $overtimePay = $hourlyRate * $overtimeHours * $overtimeRate;

        return $baseSalary + $overtimePay + $bonus;
    }

    protected function calculateTotalDeductions(): float
    {
        $grossPay = $this->calculateGrossPay();
        $taxPercentage = (float) $this->tax_percentage;
        $taxAmount = $grossPay * ($taxPercentage / 100);

        return $taxAmount
            + (float) $this->social_security
            + (float) $this->other_deductions
            + (float) $this->deductions;
    }
}
