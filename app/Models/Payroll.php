<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'pay_period_start',
        'pay_period_end',
        'base_salary',
        'overtime_pay',
        'bonus',
        'commissions',
        'allowances',
        'other_earnings',
        'tax',
        'social_security',
        'health_insurance',
        'retirement_contribution',
        'other_deductions',
        'gross_pay',
        'total_deductions',
        'net_pay',
        'payment_date',
        'payment_method',
        'payment_reference',
        'status',
        'notes',
    ];

    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'payment_date' => 'date',
        'base_salary' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'commissions' => 'decimal:2',
        'allowances' => 'decimal:2',
        'other_earnings' => 'decimal:2',
        'tax' => 'decimal:2',
        'social_security' => 'decimal:2',
        'health_insurance' => 'decimal:2',
        'retirement_contribution' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Calculate derived fields before saving
    protected static function booted()
    {
        static::saving(function ($payroll) {
            $payroll->gross_pay = $payroll->calculateGrossPay();
            $payroll->total_deductions = $payroll->calculateTotalDeductions();
            $payroll->net_pay = $payroll->gross_pay - $payroll->total_deductions;
        });
    }

    protected function calculateGrossPay(): float
    {
        return $this->base_salary
            + $this->overtime_pay
            + $this->bonus
            + $this->commissions
            + $this->allowances
            + $this->other_earnings;
    }

    protected function calculateTotalDeductions(): float
    {
        return $this->tax
            + $this->social_security
            + $this->health_insurance
            + $this->retirement_contribution
            + $this->other_deductions;
    }
}
