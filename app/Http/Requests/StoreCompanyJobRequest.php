<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'job_id' => ['required', 'integer', 'exists:job_levels,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'boss_id' => ['nullable', 'integer', 'exists:company_jobs,id'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
