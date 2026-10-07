<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $jobLevel = $this->route('job_level');

        return [
            'name' => ['sometimes', 'string', 'max:191'],
            'code' => ['nullable', 'string', 'max:191', Rule::unique('job_levels', 'code')->ignore($jobLevel?->id)],
            'description' => ['nullable', 'string', 'max:191'],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
