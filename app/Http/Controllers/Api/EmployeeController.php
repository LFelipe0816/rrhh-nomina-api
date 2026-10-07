<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Company;
use App\Models\Employee;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Company $company)
    {
        try {
            $perPage = (int) $request->query('per_page', 15);
            $perPage = $perPage > 0 ? min($perPage, 100) : 15;

            $employees = $company->employees()
                ->with('company:id,name', 'department:id,name')
                ->where('active', 1)
                ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->query('status')))
                ->when(
                    $request->filled('search'),
                    fn (Builder $query) => $query->where(function (Builder $builder) use ($request) {
                        $search = '%'.strtolower($request->query('search')).'%';

                        $builder->whereRaw('LOWER(name) LIKE ?', [$search])
                            ->orWhereRaw('LOWER(lastname) LIKE ?', [$search])
                            ->orWhereRaw('LOWER(email) LIKE ?', [$search]);
                    })
                )
                ->select('id', 'company_id', 'department_id', 'name', 'lastname', 'email', 'phone', 'job_position', 'address', 'city', 'state', 'postal_code', 'country', 'date_of_birth', 'hire_date', 'monthly_salary', 'employment_status', 'emergency_contact_name', 'emergency_contact_phone', 'notes')
                ->orderByDesc('id')
                ->paginate($perPage);
            return $this->successResponse($employees);
        } catch (\Exception|\Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Get all resources without pagination.
     */
    public function all(Request $request, Company $company)
    {
        try {
            $employees = $company->employees()
                ->with('company:id,name', 'department:id,name')
                ->where('active', 1)
                ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->query('status')))
                ->select('id', 'company_id', 'department_id', 'name', 'lastname', 'email', 'phone', 'job_position', 'monthly_salary', 'employment_status')
                ->orderByDesc('id')
                ->get();

            return $this->successResponse($employees);
        } catch (\Exception|\Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request, Company $company)
    {
        try {
            $employee = Employee::create(array_merge(
                $request->validated(),
                ['company_id' => $company->id]
            ));
            return $this->successResponse($employee, __('responses.success_operation'));
        } catch (\Exception|\Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company, Employee $employee)
    {
        try {
            return $this->successResponse($employee);
        } catch (\Exception|\Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Company $company, Employee $employee)
    {
        try {
            $employee->update($request->validated());
            return $this->successResponse($employee->fresh(), __('responses.success_operation'));
        } catch (\Exception|\Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, Employee $employee)
    {
        try {
            $employee->active = 0;
            $employee->save();
            return $this->successResponse(null, __('responses.success_operation'));
        } catch (\Exception|\Throwable $e) {
            return $this->responseNotFound($e->getMessage());
        }
    }
}
