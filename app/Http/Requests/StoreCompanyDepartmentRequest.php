<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $company = $this->route('company');

        return [
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'manager_id' => ['nullable', 'integer', 'exists:employees,id'],
            'parent_company_department_id' => [
                'nullable',
                'integer',
                'exists:companies_departments,id',
                // Validar que el padre pertenezca a la misma empresa
                function ($attribute, $value, $fail) use ($company) {
                    if ($value && $company) {
                        $parent = \App\Models\CompanyDepartment::find($value);
                        if (!$parent || $parent->company_id !== $company->id) {
                            $fail('El departamento padre debe pertenecer a la misma empresa.');
                        }
                    }
                },
            ],
            'active' => ['boolean'],
        ];
    }
}
