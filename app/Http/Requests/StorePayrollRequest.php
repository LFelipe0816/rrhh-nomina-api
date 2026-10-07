<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StorePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if (! $this->route('employee')) {
            throw ValidationException::withMessages([
                'employee' => __('validation.required', ['attribute' => 'employee']),
            ]);
        }

        return [
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after_or_equal:pay_period_start',
            'base_salary' => 'required|numeric|min:0',
            'overtime_pay' => 'sometimes|numeric|min:0',
            'bonus' => 'sometimes|numeric|min:0',
            'commissions' => 'sometimes|numeric|min:0',
            'allowances' => 'sometimes|numeric|min:0',
            'other_earnings' => 'sometimes|numeric|min:0',
            'tax' => 'sometimes|numeric|min:0',
            'social_security' => 'sometimes|numeric|min:0',
            'health_insurance' => 'sometimes|numeric|min:0',
            'retirement_contribution' => 'sometimes|numeric|min:0',
            'other_deductions' => 'sometimes|numeric|min:0',
            'payment_date' => 'required|date|after_or_equal:pay_period_end',
            'payment_method' => ['required', Rule::in(['bank_transfer', 'check', 'cash'])],
            'payment_reference' => 'nullable|string|max:255',
            'status' => ['sometimes', 'required', Rule::in(['draft', 'pending', 'paid', 'cancelled'])],
            'notes' => 'nullable|string',
        ];
    }
}