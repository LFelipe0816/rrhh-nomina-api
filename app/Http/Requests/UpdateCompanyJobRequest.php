<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'job_id' => ['sometimes', 'integer', 'exists:job_levels,id'],
            'department_id' => ['sometimes', 'integer', 'exists:departments,id'],
            'boss_id' => ['nullable', 'integer', 'exists:company_jobs,id'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
