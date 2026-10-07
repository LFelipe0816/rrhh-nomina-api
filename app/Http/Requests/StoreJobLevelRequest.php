<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'code' => ['nullable', 'string', 'max:191', 'unique:job_levels,code'],
            'description' => ['nullable', 'string', 'max:191'],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
