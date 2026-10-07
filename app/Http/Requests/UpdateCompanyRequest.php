<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $companyId = $this->route('company')?->id;

        return [
            'name' => 'sometimes|required|string|max:150',
            'legal_name' => 'sometimes|required|string|max:200',
            'rnc' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('companies', 'rnc')->ignore($companyId),
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('companies', 'email')->ignore($companyId),
            ],
            'phone' => 'nullable|string|max:30',
            'website' => 'nullable|string|max:150',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:120',
            'state' => 'nullable|string|max:120',
            'country_code' => 'nullable|string|max:5',
            'status' => 'nullable|integer|in:0,1',
        ];
    }
}
