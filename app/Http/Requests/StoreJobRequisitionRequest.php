<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequisitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_id' => ['required', 'integer', 'exists:company_jobs,id'],
            'requesting_manager_id' => ['nullable', 'integer', 'exists:employees,id'],
            'open_date' => ['required', 'date'],
            'expected_close_date' => ['nullable', 'date', 'after_or_equal:open_date'],
            'positions_count' => ['sometimes', 'integer', 'min:1'],
            'status' => ['sometimes', 'string', 'in:Open,In process,Completed,Canceled'],
        ];
    }
}
